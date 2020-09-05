<?php

namespace App;

use App\User as User;
use App\Scheme as Scheme;
use App\Question as Question;
use App\SchemeUser as SchemeUser;
use App\SchemeQuestion as SchemeQuestion;
use App\QuestionAnswer as QuestionAnswer;

class Algorithm
{
    private static $SPECIAL_QUESTION_THRESHOLD = 0;

    private $scheme;
    private $schemeQuestionIDs;
    private $questionsMapping;
    private $questionsWeighting;
    private $unpairedUsersPreferences;

    /**
     * Algorithm constructor
     * @param Scheme|null $scheme The corresponding scheme for this algorithm
     */
    public function __construct(Scheme $scheme = null)
    {
        $this->scheme = $scheme;
    }

    /**
     * @param Scheme $scheme The corresponding scheme for this algorithm and where it gets its data from
     * @return Algorithm An algorithm for the specified scheme
     */
    public static function newAlgorithmByScheme(Scheme $scheme): Algorithm
    {
        $newAlgorithm = new Algorithm($scheme);
        $newAlgorithm->questionsMapping = self::getQuestionsMapping();
        $newAlgorithm->questionsWeighting = $newAlgorithm->getQuestionsWeightings();
        $newAlgorithm->unpairedUsersPreferences = $newAlgorithm->getUnpairedUsersPreferences();

        return $newAlgorithm;
    }

    /**
     * @param Scheme $scheme The corresponding scheme for this algorithm
     * @param array $questionsMapping A mapping of question IDs to their details
     * @param array $questionsWeighting A mapping of question IDs to their weightings
     * @param array $unpairedUsersPreferences A mapping of user type IDs to an array of the users of that type and their preferences
     * @return Algorithm An algorithm with the specified parameters
     */
    public static function newAlgorithm(Scheme $scheme, array $questionsMapping, array $questionsWeighting, array $unpairedUsersPreferences): Algorithm
    {
        $newAlgorithm = new Algorithm($scheme);
        $newAlgorithm->initialiseVariables($questionsMapping, $questionsWeighting, $unpairedUsersPreferences);
        return $newAlgorithm;
    }

    /**
     * @param array $questionsMapping A mapping of question IDs to their details
     * @param array $questionsWeighting A mapping of question IDs to their weightings
     * @param array $unpairedUsersPreferences A mapping of user type IDs to an array of the users of that type and their preferences
     * @throws \Exception
     */
    public final function initialiseVariables(array $questionsMapping, array $questionsWeighting, array $unpairedUsersPreferences, Scheme $scheme = null)
    {
        if (isset($scheme)) {
            if (isset($this->scheme)) {
                throw new \Exception('Algorithm scheme already initialised');
            }
            $this->scheme = $scheme;
        }
        if (isset($this->questionsMapping) || isset($this->questionsWeighting) || isset($this->unpairedUsersPreferences)) {
            throw new \Exception('Algorithm variables already initialised');
        }
        $this->questionsMapping = $questionsMapping;
        $this->questionsWeighting = $questionsWeighting;
        $this->unpairedUsersPreferences = $unpairedUsersPreferences;
    }

    /**
     * @return array A mapping of all question IDs to their details
     */
    public static function getQuestionsMapping(): array
    {
        $questions = [];
        foreach (Question::all() as $question) {
            $questions[$question->id] = [
                'type_id' => $question->type_id,
                'validation' => $question->getValidation()
            ];
        }
        return $questions;
    }

    /**
     * @return array A mapping of question IDs to their weightings
     */
    public final function getQuestionsWeightings(): array
    {
        $this->schemeQuestionIDs = [];
        $questionWeightings = [];
        foreach (SchemeQuestion::whereSchemeId($this->scheme->id)->get() as $schemeQuestion) {
            $this->schemeQuestionIDs[$schemeQuestion->id] = $schemeQuestion->question_id;
            $questionWeightings[$schemeQuestion->question_id] = $schemeQuestion->weight;
        }
        return $questionWeightings;
    }

    /**
     * Example: [1 => [[user1, user2], [1 => null, 2 => {'max_newbies': 2}]]
     * @return array A mapping of user type IDs to an array of the users of that type and their preferences.
     */
    public final function getUnpairedUsersPreferences(): array
    {
        $unpairedSchemeUsers = SchemeUser::whereSchemeId($this->scheme->id)->whereApproved(true)->whereNull('pairing_id')->get();

        $newbiesIDs = [];
        $newbiesPreferences = [];
        $buddiesIDs = [];
        $buddiesPreferences = [];
        foreach ($unpairedSchemeUsers as $unpairedSchemeUser) {
            if ($unpairedSchemeUser->user_type_id == 1) {
                $newbiesIDs[] = $unpairedSchemeUser->user_id;
                $newbiesPreferences[$unpairedSchemeUser->user_id] = $unpairedSchemeUser->getPreferences();
            } else if ($unpairedSchemeUser->user_type_id == 2) {
                $buddiesIDs[] = $unpairedSchemeUser->user_id;
                $buddiesPreferences[$unpairedSchemeUser->user_id] = $unpairedSchemeUser->getPreferences();
            }
        }

        $unpairedNewbies = User::whereIn('id', $newbiesIDs)->get()->all();
        $unpairedBuddies = User::whereIn('id', $buddiesIDs)->get()->all();

        return [1 => [$unpairedNewbies, $newbiesPreferences], 2 => [$unpairedBuddies, $buddiesPreferences]];
    }

    /**
     * Create a mapping from buddies to newbies
     * @return array The created mapping
     */
    public function createMapping(): array
    {
        $buddies = $this->unpairedUsersPreferences[2][0];
        $newbies = $this->unpairedUsersPreferences[1][0];
        $usersAnswers = $this->getAnswers();

        switch ($maxBuddies = $this->scheme->getMaxBuddies()) {
            case 1:
                return $this->createIndividualMapping($buddies, $newbies, $usersAnswers);
            case 2:
                return $this->createParentMapping($buddies, $newbies, $usersAnswers);
            default:
                throw new \InvalidArgumentException("Scheme with $maxBuddies buddies per matching has an illegal number of buddies per matching.");
        }
    }

    /**
     * Assign each buddy to a number of newbies
     *
     * @param array $buddies An array of User instances representing buddies
     * @param array $newbies An array of User instances representing newbies
     * @param array $usersAnswers An associative array mapping user IDs to an associative array of the answers to each question.
     * @return array An array containing 2 arrays. The first being an array of all the buddies.
     * And the second being an array of all the newbie groups. Matched up by index.
     */
    public function createIndividualMapping(array $buddies, array $newbies, array $usersAnswers): array
    {
        // 1: Initialise values
        $mapping = self::createInitialMapping($buddies);
        $distanceMatrix = [];
        $buddiesMaxNewbies = $this->getBuddiesMaxNewbies($buddies);

        // 2: For each buddy in B
        for ($i = 0; $i < count($buddies); ++$i)
            // 2.1: For each newbie in N
            for ($j = 0; $j < count($newbies); ++$j)
                // 2.1.1: Set A[i][j] to the distance between the buddy and newbie
                $distanceMatrix[$i][$j] = $this->userDistance($usersAnswers, $buddies[$i], $newbies[$j]);

        // 3: Loop infinitely
        while (true) {
            // 3.1: Select the smallest value in the matrix
            $minDistance = PHP_INT_MAX;
            $minColumnNumber = null;
            $minRowNumber = null;

            for ($x = 0; $x < count($buddies); ++$x)
                for ($y = 0; $y < count($newbies); ++$y)
                    if (($distanceMatrix[$x][$y] !== null) && $distanceMatrix[$x][$y] < $minDistance) {
                        $minDistance = $distanceMatrix[$x][$y];
                        $minColumnNumber = $x;
                        $minRowNumber = $y;
                    }

            // 3.2: If there is no smallest value: Return M
            if ($minDistance === PHP_INT_MAX)
                return $mapping; // Can only be reached if all newbies or all buddies have been assigned

            // 3.3: Set A[k][y] to null for all k
            for ($k = 0; $k < count($buddies); ++$k) $distanceMatrix[$k][$minRowNumber] = null;

            // 3.4: Add the newbie for that row to the array the buddy for that column maps to
            array_push($mapping[1][array_search([$buddies[$minColumnNumber]], $mapping[0])], $newbies[$minRowNumber]);

            // 3.5: If the size of that set is equal to the maximum number of newbies for that buddy:
            // Set A[x][k] to null for all k
            if ($buddiesMaxNewbies[$buddies[$minColumnNumber]->id] == count($mapping[1][array_search([$buddies[$minColumnNumber]], $mapping[0])]))
                for ($k = 0; $k < count($newbies); ++$k) $distanceMatrix[$minColumnNumber][$k] = null;
        }
    }

    /**
     * Put each buddy into a buddy pair then assign the pair to a number of newbies
     *
     * @param array $buddies An array of User instances representing buddies
     * @param array $newbies An array of User instances representing newbies
     * @param array $usersAnswers An associative array mapping user IDs to an associative array of the answers to each question.
     * @return array An array containing 2 arrays. The first being an array of all the buddy pairs.
     * And the second being an array of all the newbie groups. Matched up by index.
     */
    public function createParentMapping(array $buddies, array $newbies, array $usersAnswers): array
    {
        // 1: Initialise values
        $mapping = self::createInitialMapping($buddies);
        $distanceMatrix = [];
        $buddyPairs = [];

        // 2: For each buddy in B
        for ($i = 0; $i < count($buddies); ++$i)
            // 2.1: For each buddy in B
            for ($j = 0; $j < count($buddies); ++$j)
                // 2.1.1: Set A[i][j] to the distance between the 2 buddies
                // If i = j, set it to null instead
                $distanceMatrix[$i][$j] = $i === $j ? null : $this->userDistance($usersAnswers, $buddies[$i], $buddies[$j]);

        // 3: Loop infinitely
        while (true) {
            // 3.1: Select the smallest value in the matrix
            $minDistance = PHP_INT_MAX;
            $minColumnNumber = null;
            $minRowNumber = null;

            for ($x = 0; $x < count($buddies); ++$x)
                for ($y = 0; $y < count($buddies); ++$y)
                    if (($distanceMatrix[$x][$y] !== null) && $distanceMatrix[$x][$y] < $minDistance) {
                        $minDistance = $distanceMatrix[$x][$y];
                        $minColumnNumber = $x;
                        $minRowNumber = $y;
                    }

            // 3.2: If there is no smallest value: Break from 4
            if ($minDistance === PHP_INT_MAX) break;

            // 3.3: Set A[k][y] to null for all k
            // 3.4: Set A[x][k] to null for all k
            for ($k = 0; $k < count($buddies); ++$k) {
                $distanceMatrix[$k][$minRowNumber] = null;
                $distanceMatrix[$minColumnNumber][$k] = null;
            }

            // 3.5: Add the pair to the list of buddy pairs
            array_push($buddyPairs, [$buddies[$minColumnNumber], $buddies[$minRowNumber]]);
        }

        // 4: Set A to ∅
        $distanceMatrix = [];

        // 5: For each pair in B'
        for ($i = 0; $i < count($buddyPairs); ++$i) {
            // 5.1: Map the pair to ∅ in M
            array_push($mapping[0], $buddyPairs[$i]);
            array_push($mapping[1], []);

            // 5.2: For each newbie in N
            for ($j = 0; $j < count($newbies); ++$j)
                // 5.2.1: Set A[i][j] to the distance between the pair and the newbie
                $distanceMatrix[$i][$j] = $this->pairAndUserDistance($usersAnswers, $buddyPairs[$i], $newbies[$j]);
        }

        $schemeMaxNewbies = $this->scheme->getMaxNewbies();
        // 6: Loop infinitely
        while (true) {
            // 6.1: Select the smallest value in the matrix
            $minDistance = PHP_INT_MAX;
            $minColumnNumber = null;
            $minRowNumber = null;

            for ($x = 0; $x < count($buddyPairs); ++$x)
                for ($y = 0; $y < count($newbies); ++$y)
                    if (($distanceMatrix[$x][$y] !== null) && $distanceMatrix[$x][$y] < $minDistance) {
                        $minDistance = $distanceMatrix[$x][$y];
                        $minColumnNumber = $x;
                        $minRowNumber = $y;
                    }

            // 6.2: If there is no smallest value: Return M
            if ($minDistance === PHP_INT_MAX)
                return $mapping; // Can only be reached if all newbies or all buddy pairs have been assigned

            // 6.3: Set A[k][y] to null for all k
            for ($k = 0; $k < count($buddyPairs); ++$k) $distanceMatrix[$k][$minRowNumber] = null;

            // 6.4: Add the newbie for that row to the array the buddy for that column maps to
            array_push($mapping[1][array_search($buddyPairs[$minColumnNumber], $mapping[0])], $newbies[$minRowNumber]);

            // 6.5: If the size of that array is equal to the maximum number of newbies for that buddy:
            // Set A[x][k] to null for all k
            if ($schemeMaxNewbies === count($mapping[1][array_search($buddyPairs[$minColumnNumber], $mapping[0])]))
                for ($k = 0; $k < count($newbies); ++$k) $distanceMatrix[$minColumnNumber][$k] = null;
        }
    }

    /**
     * Create a mapping (by index), represented by an array containing and array of keys and an array of values
     * @param $keys array The array of keys
     * @return array An array that represents the mapping
     */
    public static function createInitialMapping(array $keys): array
    {
        $initialMapping = [[], []];

        foreach ($keys as $key) {
            array_push($initialMapping[0], [$key]);
            array_push($initialMapping[1], []);
        }

        return $initialMapping;
    }

    /**
     * @return array An associative array mapping user IDs to an associative array of question IDs mapped to the user's answer.
     */
    public function getAnswers(): array
    {
        $userIDs = [];
        foreach ($this->unpairedUsersPreferences as $userTypeID => $unpairedUsersOfTypePreferences) {
            foreach ($unpairedUsersOfTypePreferences[0] as $unpairedUser) {
                $userIDs[] = $unpairedUser->id;
            }
        }

        $usersAnswers = QuestionAnswer::whereIn('user_id', $userIDs)->whereIn('scheme_question_id', array_keys($this->schemeQuestionIDs))->get()->groupBy('user_id')->all();
        foreach ($usersAnswers as $userID => $userQuestionAnswers) {
            $decodedUserAnswers = [];
            foreach ($userQuestionAnswers as $userQuestionAnswer) {
                $questionID = $this->schemeQuestionIDs[$userQuestionAnswer->scheme_question_id];
                $decodedUserAnswers[$questionID] = $userQuestionAnswer->getAnswer();
            }
            $usersAnswers[$userID] = $decodedUserAnswers;
        }
        return $usersAnswers;
    }

    /**
     * @param array $buddies
     * @return array An associative array mapping buddy IDs to their preference of maximum newbies (default value is the max for the scheme).
     */
    public function getBuddiesMaxNewbies(array $buddies): array
    {
        $buddiesMaxNewbies = [];
        $schemeMaxNewbies = $this->scheme->getMaxNewbies();
        foreach ($buddies as $buddy) {
            $preferences = $this->unpairedUsersPreferences[2][1][$buddy->id];
            $maxNewbies = null;
            if (isset($preferences) && array_key_exists('max_newbies', $preferences))
                $maxNewbies = min($schemeMaxNewbies, $preferences['max_newbies']);
            else
                $maxNewbies = $schemeMaxNewbies;
            $buddiesMaxNewbies[$buddy->id] = $maxNewbies;
        }
        return $buddiesMaxNewbies;
    }

    /**
     * @param $usersAnswers array A mapping of user IDs to their question answers
     * @param $user1 User The first user
     * @param $user2 User the first user
     * @return float The euclidean distance between 2 users based on their questionnaire results
     */
    public function userDistance(array $usersAnswers, User $user1, User $user2): float
    {
        $distances = $this->userDistances($usersAnswers, $user1, $user2);

        return self::vectorMagnitude($distances);
    }

    /**
     * @param array $arr A mathematical vector represented as an array
     * @return float The magnitude of the vector
     */
    public static function vectorMagnitude(array $arr): float
    {
        $squareSum = 0;
        foreach ($arr as $value) $squareSum += $value * $value;

        return sqrt($squareSum);
    }

    /**
     * @param array $usersAnswers A mapping of user IDs to their question answers
     * @param array $pair The pair
     * @param User $user The user
     * @return float The distance between the pair's answers and the user's answers
     */
    public function pairAndUserDistance(array $usersAnswers, array $pair, User $user): float
    {
        $pairFirstDistances = $this->userDistances($usersAnswers, $pair[0], $user);
        $pairSecondDistances = $this->userDistances($usersAnswers, $pair[1], $user);

        return (self::vectorMagnitude($pairFirstDistances) + self::vectorMagnitude($pairSecondDistances)) / (2 * count($pairFirstDistances));
    }

    /**
     * @param $usersAnswers array A mapping of user IDs to their question answers
     * @param $user1 User The first user
     * @param $user2 User the first user
     * @return array The distance between each of the 2 users' answers
     */
    public function userDistances(array $usersAnswers, User $user1, User $user2): array
    {
        $user1Answers = $usersAnswers[$user1->id];
        $user2Answers = $usersAnswers[$user2->id];
        $distances = [];

        foreach ($user1Answers as $user1QuestionID => $user1Answer) {
            $user2Answer = $user2Answers[$user1QuestionID];
            if (isset($user2Answer)) {
                $questionDetails = $this->questionsMapping[$user1QuestionID];
                $questionTypeID = $questionDetails['type_id'];
                if ($questionTypeID < self::$SPECIAL_QUESTION_THRESHOLD)
                    $distances[$user1QuestionID] = $this->specialDistance($user1QuestionID, $questionTypeID, $user1, $user2, $user1Answer, $user2Answer);
                else
                    $distances[$user1QuestionID] = $this->answerDistance($user1QuestionID, $questionTypeID, $user1Answer, $user2Answer);
            }
        }

        return $distances;
    }

    /**
     * Calculate the distance between answers to special questions i.e. hardcoded questions.
     * These questions may use user data alongside answers for that question.
     * @param $questionID int The ID of the special question
     * @param $questionTypeID int The ID of the type of the special question
     * @param $user1 User The first user
     * @param $user2 User The second user
     * @param $user1Answer array The first user's answer
     * @param $user2Answer array The second user's answer
     * @return float The normalised and weighted distance between the answers
     */
    public function specialDistance(int $questionID, int $questionTypeID, User $user1, User $user2, array $user1Answer, array $user2Answer): float
    {
        switch ($questionTypeID) {
            case -2:
                $returnValue = self::questionSpecial2Distance($user1, $user2, $user1Answer[0], $user2Answer[0]);
                break;
            case -1:
                $returnValue = self::questionSpecial1Distance($user1, $user2, $user1Answer[0], $user2Answer[0]);
                break;
            default:
                throw new \InvalidArgumentException("Special question with ID $questionID has unknown type $questionTypeID");
        }

        return $returnValue * $this->getDistanceWeight($questionID, $questionTypeID) / $this->getNormalisationValue($questionID, $questionTypeID);
    }

    /**
     * Calculate the distance between 2 users answering the question "Do you have a gender preference?"
     * Where 0 represents "No preference" and 1 represents "Same gender"
     * @param $user1 User The first user
     * @param $user2 User The second user
     * @param $user1Answer int The first user's answer
     * @param $user2Answer int The second user's answer
     * @return int 0 if there is a gender preference and its criteria are met, 1 otherwise
     */
    public static function questionSpecial1Distance(User $user1, User $user2, int $user1Answer, int $user2Answer): int
    {
        return (($user1Answer || $user2Answer) && // True if either user has a "same gender" preference
            ($user1->gender === 4 || //$user2->gender === 4 || // True if either user has not specified their gender
                $user1->gender != $user2->gender)); // True if the users don't have the same gender
    }

    /**
     * Calculate the distance between 2 users answering the question
     * "Do you prefer to be matched with users with a similar age?"
     * Where 0 represents "No preference" and 1 represents "Yes"
     *
     * If the age difference matters to either user but a user hasn't set
     * their age, the difference is assumed to be large
     * @param $user1 User The first user
     * @param $user2 User The second user
     * @param $user1Answer int The first user's answer
     * @param $user2Answer int The second user's answer
     * @return float 0 if there is no preference, the absolute differences
     * between the ages if the ages are specified, 100 otherwise
     */
    public static function questionSpecial2Distance(User $user1, User $user2, int $user1Answer, int $user2Answer): float
    {
        if ($user1Answer || $user2Answer) // True if either user has a "similar age" preference
            if (is_null($user1Age = $user1->getAge()) || is_null($user2Age = $user2->getAge())) // True if either user hasn't set their age
                return 100;
            else return abs($user1Age - $user2Age);
        else return 0;
    }

    /**
     * @param $questionID int The ID of the question the answers are to
     * @param $questionTypeID int The ID of the type of the specified question
     * @param $answer1 array The first answer
     * @param $answer2 array The second answer
     * @return float The normalised and weighted distance between the answers
     */
    public function answerDistance(int $questionID, int $questionTypeID, array $answer1, array $answer2): float
    {
        switch ($questionTypeID) {
            case 1:
                $returnValue = self::questionType1Distance($answer1, $answer2);
                break;
            case 2:
                $returnValue = self::questionType2Distance($answer1, $answer2);
                break;
            case 3:
                $returnValue = self::questionType3Distance($answer1, $answer2);
                break;
            case 4:
                $returnValue = self::questionType4Distance($answer1[0], $answer2[0]);
                break;
            case 5:
                $returnValue = self::questionType5Distance($answer1[0], $answer2[0]);
                break;
            case 6:
                $returnValue = self::questionType6Distance($answer1, $answer2);
                break;
            case 7:
                $returnValue = self::questionType7Distance($answer1[0], $answer2[0]);
                break;
            case 8:
                $returnValue = self::questionType8Distance($answer1, $answer2);
                break;
            default:
                throw new \InvalidArgumentException("Question with ID $questionID has unknown type $questionTypeID");
        }

        return $returnValue * $this->getDistanceWeight($questionID, $questionTypeID) / $this->getNormalisationValue($questionID, $questionTypeID);
    }

    /**
     * Return the euclidean distance between 2 n-dimensional points in the form [x, y, z, ...]
     * @param $answer1 array The first point
     * @param $answer2 array The second point
     * @return float The euclidean distance between the points
     */
    public static function questionType1Distance(array $answer1, array $answer2): float
    {
        $temp = 0;
        for ($i = 0; $i < count($answer1); ++$i) $temp += pow($answer1[$i] - $answer2[$i], 2);
        return sqrt($temp);
    }

    /**
     * Return the euclidean distance between 2 n-dimensional points in the form [x, y, z, ...]
     * @param $answer1 array The first point
     * @param $answer2 array The second point
     * @return float The euclidean distance between the points
     */
    public static function questionType2Distance(array $answer1, array $answer2): float
    {
        $temp = 0;
        for ($i = 0; $i < count($answer1); ++$i) $temp += pow($answer1[$i] - $answer2[$i], 2);
        return sqrt($temp);
    }

    /**
     * Use the Haversine Formula to calculate the euclidean distance between 2 locations
     * on Earth that are represented by arrays in the form [latitude, longitude]
     * @param $answer1 array The first location
     * @param $answer2 array The second location
     * @return float The surface distance between the locations in kilometers
     */
    public static function questionType3Distance(array $answer1, array $answer2): float
    {
        // TODO: Move constants to another file
        $EARTH_RADIUS = 6373;
        $DEG_TO_RAD = 0.017453292519943295; // M_PI / 180

        $lat1 = $answer1[0] * $DEG_TO_RAD;
        $lat2 = $answer2[0] * $DEG_TO_RAD;
        $lon1 = $answer1[1] * $DEG_TO_RAD;
        $lon2 = $answer2[1] * $DEG_TO_RAD;

        $a = pow((sin(($lat2 - $lat1) / 2)), 2) + cos($lat1) * cos($lat2) * pow((sin(($lon2 - $lon1) / 2)), 2);
        return $EARTH_RADIUS * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Return the distance (absolute difference) between 2 values
     * @param $answer1 float The first value
     * @param $answer2 float The second value
     * @return float The distance between the values
     */
    public static function questionType4Distance(float $answer1, float $answer2): float
    {
        return abs($answer1 - $answer2);
    }

    /**
     * Note: This function is deprecated
     *
     * Return the time between 2 dates represented by strings in the format YYYY-MM-DD
     * @param $answer1 string The first date
     * @param $answer2 string The second date
     * @return int The time between the dates in positive seconds
     */
    public static function questionType5Distance(string $answer1, string $answer2): int
    {
        trigger_error("Deprecated function called.", E_USER_DEPRECATED);
        return abs(strtotime('yy-mm-dd', $answer1) - strtotime('yy-mm-dd', $answer2));
    }

    /**
     * Return the distance between 2 lists of boolean answers represented as an array
     * containing 1s or 0s, treating the arrays as n-dimensional points.
     * @param $answer1 array The first list of boolean answers
     * @param $answer2 array The second list of boolean answers
     * @return float The euclidean distance between the answers
     */
    public static function questionType6Distance(array $answer1, array $answer2): float
    {
        $returnValue = 0;
        for ($i = 0; $i < count($answer1); ++$i)
            $returnValue += ($answer1[$i] xor $answer2[$i]); // 1 if the answers are different, 0 if the same
        return sqrt($returnValue);
    }

    /**
     * Return the distance (absolute difference) between 2 values
     * @param $answer1 float The first value
     * @param $answer2 float The second value
     * @return float The distance between the values
     */
    public static function questionType7Distance(float $answer1, float $answer2): float
    {
        return abs($answer1 - $answer2);
    }

    /**
     * Return the distance between 2 colours represented by their hex code using the RGB colour model.
     * The distance between 2 colours is the Euclidean distance between the colours
     * when they're represented as 3 dimensional points in the form (R, G, B).
     * @param $answer1 array The first colour
     * @param $answer2 array The second colour
     * @return float The distance between the colours
     */
    public static function questionType8Distance(array $answer1, array $answer2): float
    {
        $colour1 = sscanf($answer1[0], "#%02x%02x%02x");
        $colour2 = sscanf($answer2[0], "#%02x%02x%02x");

        return sqrt(pow($colour1[0] - $colour2[0], 2)
            + pow($colour1[1] - $colour2[1], 2)
            + pow($colour1[2] - $colour2[2], 2));
    }

    /**
     * Return the normalisation value for a given question with a given question ID. The normalisation value
     * being the value distances for a given question are divided by so that the maximum possible distance is 1.
     * @param int $questionID The question ID
     * @param int $questionTypeID The type ID of the specified question
     * @return float The normalisation value
     */
    public function getNormalisationValue(int $questionID, int $questionTypeID): float
    {
        switch ($questionTypeID) {
            case -2:
                return 1;
            case -1:
                return 1;
            case 1:
                return 4 * sqrt($this->questionsMapping[$questionID]['validation']['options']);
            case 2:
                return sqrt((pow($this->questionsMapping[$questionID]['validation']['options'], 3) - $this->questionsMapping[$questionID]['validation']['options']) / 3);
            case 3:
                return 20015.086796; // TODO: Retrieve constant from database
            case 4:
                return 365; // TODO: Calculate (max - min) using information retrieved from database
            case 5:
                return $this->questionsMapping[$questionID]['validation']['max'] - $this->questionsMapping[$questionID]['validation']['min'];
            case 6:
                return $this->questionsMapping[$questionID]['validation']['options'];
            case 7:
                return $this->questionsMapping[$questionID]['validation']['max'] - $this->questionsMapping[$questionID]['validation']['min'];
            case 8:
                return 441.67295593006; // sqrt(3*255*255) // TODO: Retrieve constant from database
            default:
                throw new \InvalidArgumentException("Question with ID $questionID has unknown type " . $questionTypeID);
        }
    }

    /**
     * Return the weight for a question with a given question ID
     * Where the weight exaggerates the differences between users
     * @param int $questionID The question ID
     * @param int $questionTypeID The type ID of the specified question
     * @return float The weight
     */
    public function getDistanceWeight(int $questionID, int $questionTypeID): float
    {
        switch ($questionTypeID) {
            case -2:
                $defaultWeight = 100;
                break;
            case -1:
                $defaultWeight = 1e6;
                break;
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
                $defaultWeight = 1;
                break;
            default:
                throw new \InvalidArgumentException("Question with ID $questionID has unknown type " . $questionTypeID);
        }

        return $defaultWeight * $this->questionsWeighting[$questionID];
    }
}
