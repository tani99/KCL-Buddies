<?php

namespace Tests\Unit\Algorithm;

use App\Algorithm;
use App\Scheme;
use App\User;
use Tests\TestCase;

class DistancesTest extends TestCase
{
    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be very small due to minor differences
     */
    public function testUserDistance1()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 256;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 257;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [256 => null]], 2 => [[$user2], [257 => null]]]);
        $result = $algorithm->userDistance(
            [
                256 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [61.7420487, 8.4405546],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#fafa00'],
                    7 => [0]
                ],
                257 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.47837, 156.8949893],
                    4 => [26],
                    5 => [6, 5, 4, 3, 1, 2],
                    6 => ['#fa0000'],
                    7 => [0]
                ]
            ], $user1, $user2);
        $this->assertEquals(0.8081934276537, $result);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be very large due to major differences
     * due to one user specifying similar age
     */
    public function testUserDistance2()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistance(
            [
                1 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.4898009, 152.4098799],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                2 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.47837, 156.8949893],
                    4 => [26],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#00fb00'],
                    7 => [1]
                ]
            ], $user1, $user2);
        $this->assertEquals(1000000.5, $result, '', 0.1);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be large due to  differences
     * but not very large as gender is set to no preference
     */
    public function testUserDistance3()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 30;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 44;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [40 => null]], 2 => [[$user2], [44 => null]]]);
        $result = $algorithm->userDistance(
            [
                30 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.4898009, 152.4098799],
                    4 => [22],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#FFFFFF'],
                    7 => [0]
                ],
                44 => [
                    0 => [0],
                    1 => [2, 1, 0, -2, 1, -1],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.47837, 156.8949893],
                    4 => [32],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#000000'],
                    7 => [1]
                ]
            ], $user1, $user2);
        $this->assertEquals(1000.0007162703, $result, '', 0.00000001);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be very small and are most likely to be matched
     * due to similar results and both stating preferences in gender and age
     */
    public function testUserDistance4()
    {
        $user1 = new User();
        $user1->gender = 2; //user1 is a female
        $user1->id = 19;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 20;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [19 => null]], 2 => [[$user2], [20 => null]]]);
        $result = $algorithm->userDistance(
            [
                19 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [20.0121253, 64.446581],
                    4 => [18],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                20 => [
                    0 => [0],
                    1 => [2, 1, -1, 1, 0, -2],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [11.47837, 156.8949893],
                    4 => [17],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#006100'],
                    7 => [0]
                ]
            ], $user1, $user2);
        $this->assertEquals(0.72330179034513, $result);
    }


    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be small due to  minor differences
     */
    public function testUserDistance5()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 55;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 56;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [55 => null]], 2 => [[$user2], [56 => null]]]);
        $result = $algorithm->userDistance(
            [
                55 => [
                    0 => [0],
                    1 => [-2, 1, 1, 0, -1, 2],
                    2 => [4, 1, 2, 3, 5, 6],
                    3 => [11.4898009, 152.4098799],
                    4 => [18],
                    5 => [1, 4, 5, 6, 2, 3],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                56 => [
                    0 => [0],
                    1 => [2, -2, 1, -1, 1, 0,],
                    2 => [1, 5, 2, 3, 4, 6],
                    3 => [11.47837, 156.8949893],
                    4 => [19],
                    5 => [1, 4, 5, 6, 3, 2],
                    6 => ['#00fb00'],
                    7 => [0]
                ]
            ], $user1, $user2);
        $this->assertEquals(0.99202054734639, $result);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be very large due to major differences
     */
    public function testUserDistance6()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistance(
            [
                1 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.4898009, 152.4098799],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [1]
                ],
                2 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.47837, 156.8949893],
                    4 => [26],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#00fb00'],
                    7 => [1]
                ]
            ], $user1, $user2);
        $this->assertEquals(1000.0000972756, $result, '', 0.00000001);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be 0 because answers are identical
     */
    public function testUserDistance7()
    {
        $user1 = new User();
        $user1->gender = 4; //user1 is a unspecified
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistance(
            [
                1 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                2 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 5, 4, 1, 3, 6,],
                    6 => ['#003c00'],
                    7 => [0]
                ]
            ], $user1, $user2);
        $this->assertEquals(0, $result);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be very large due to major differences
     * due to gender and age
     */
    public function testUserDistance8()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a other
        $user1->id = 71;
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        $user2->id = 72;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [71 => null]], 2 => [[$user2], [72 => null]]]);
        $result = $algorithm->userDistance(
            [
                71 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [11.4898009, 152.4098799],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [1]
                ],
                72 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [5, 1, 2, 3, 4, 6],
                    3 => [65.351167, -59.3491597],
                    4 => [23],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c01'],
                    7 => [1]
                ]
            ], $user1, $user2);
        $this->assertEquals(1000.0002976909, $result, '', 0.00000001);
    }

    /**
     * Test to check the euclidean distance between 2 users based on their questionnaire results
     * The distance between two users would be small due to some differences
     */
    public function testUserDistance9()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a other
        $user1->id = 431;
        $user2 = new User();
        $user2->gender = 3; //user2 is a other
        $user2->id = 472;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [431 => null]], 2 => [[$user2], [472 => null]]]);
        $result = $algorithm->userDistance(
            [
                431 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [30.0497079, 60.3336572],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                472 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [6, 5, 4, 3, 2, 1],
                    3 => [20.0121253, 64.446581],
                    4 => [17],
                    5 => [5, 4, 2, 1, 3, 6],
                    6 => ['#003c01'],
                    7 => [0]
                ]
            ], $user1, $user2);
        $this->assertEquals(1.0018049916289, $result, '', 0.00000001);
    }


    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForBothMalesIsMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 1; //user2 is a male
        //male, male - same gender
        $result1 = Algorithm::questionSpecial1Distance($user1, $user1, 1, 1);
        $this->assertEquals(0, $result1);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForBothFemalesIsMet()
    {
        $user1 = new User();
        $user1->gender = 2; //user1 is a female
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        //female, female - same gender
        $result2 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 1);
        $this->assertEquals(0, $result2);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForBothOtherIsMet()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a other
        $user2 = new User();
        $user2->gender = 3; //user2 is a other
        //other, other
        $result3 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 1);
        $this->assertEquals(0, $result3);
    }


    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOneMaleIsMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 1; //user2 is a male
        //male, male - one no preference, one same gender
        $result4 = Algorithm::questionSpecial1Distance($user1, $user2, 0, 1);
        $this->assertEquals(0, $result4);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOneFemaleIsMet()
    {
        $user1 = new User();
        $user1->gender = 2; //user1 is a female
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        //female,female - no preference, same gender
        $result5 = Algorithm::questionSpecial1Distance($user1, $user2, 0, 1);
        $this->assertEquals(0, $result5);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOneOtherIsMet()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a female
        $user2 = new User();
        $user2->gender = 3; //user2 is a female
        //other,other - no preference, same gender
        $result6 = Algorithm::questionSpecial1Distance($user1, $user2, 0, 1);
        $this->assertEquals(0, $result6);
    }


    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForUnspecifiedIsMet()
    {
        $user1 = new User();
        $user1->gender = 4; //user1 is a unspecified
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        //unspecified, unspecified
        $result7 = Algorithm::questionSpecial1Distance($user1, $user1, 0, 0);
        $this->assertEquals(0, $result7);
    }


    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForMaleAnFemaleIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user3 = new User();
        $user3->gender = 3; //user3 is a other
        $user4 = new User();
        $user4->gender = 4; //user4 is a unspecified
        //male, female - one no preference, one same gender
        $result = Algorithm::questionSpecial1Distance($user1, $user2, 0, 1);
        $this->assertEquals(1, $result);


    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForMaleAndOtherIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 3; //user2 is a other
        //male, other - one no preference, one same gender
        $result1 = Algorithm::questionSpecial1Distance($user1, $user2, 0, 1);
        $this->assertEquals(1, $result1);
    }


    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForFemaleAndMaleIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        //female, male - one same gender, one no preference,
        $result2 = Algorithm::questionSpecial1Distance($user2, $user1, 1, 0);
        $this->assertEquals(1, $result2);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOtherAndFemaleIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a other
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        //other, female - one same gender, one no preference,
        $result3 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 0);
        $this->assertEquals(1, $result3);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOtherAndUnspecifiedIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 3; //user1 is a other
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        //other, female - one same gender, one no preference,
        $result3 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 0);
        $this->assertEquals(1, $result3);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForOtherAndMaleIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 3; //user2 is a other
        //other, male - one same gender, one no preference,
        $result4 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 0);
        $this->assertEquals(1, $result4);
    }

    public function testQuestionSpecial1DistanceCriteriaOfSameGenderForUnspecifiedAndMaleIsNotMet()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        //other, male - one same gender, one no preference,
        $result5 = Algorithm::questionSpecial1Distance($user1, $user2, 1, 0);
        $this->assertEquals(1, $result5);
    }

    public function testQuestionSpecial2DistanceCorrectlyGivesDifferenceWhenAgeNotSpecified()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        $result = Algorithm::questionSpecial2Distance($user1, $user2, 22, 18);
        $this->assertEquals(100, $result);
    }

    public function testQuestionSpecial2DistanceCorrectlyGivesDifferenceWhenNoPreference()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user2 = new User();
        $user2->gender = 4; //user2 is a unspecified
        $result = Algorithm::questionSpecial2Distance($user1, $user2, 0, 0);
        $this->assertEquals(0, $result);
    }

    /*
     *  Question Type 1 Tests
     */

    /**
     * Tests normal arrays of scores (Question type 1)
     * Takes two arrays with the same values.
     * Ensures that the distance between them is 0.
     */
    public function testType1ZeroDistance()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 5], [1, 2, 3, 4, 5]);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 1)
     * Takes two arrays with different values.
     * Ensures that the distance between them is not 0.
     */
    public function testType1NotZeroDistance()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 5], [1, 2, 3, 4, 2]);
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Tests that the distance between arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceSimpleArrays1()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4], [1, 3, 3, 4]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceSimpleArrays2()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between long arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceLongArrays1()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 1, 1, 3, 1, 3, 5, 3, 1], [1, 3, 3, 4, 1, 2, 3, 1, 3, 5, 3, 1]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between long arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceLongArrays2()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 5, 5, 4, 3, 2, 1, 1, 1], [4, 3, 2, 1, 1, 1, 3, 1, 2, 3, 3, 5]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between short arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceShortArrays1()
    {
        $distance = Algorithm::questionType1Distance([1], [2]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between short arrays of scores is positive (Question type 1).
     */
    public function testType1NotNegativeDistanceShortArrays2()
    {
        $distance = Algorithm::questionType1Distance([1], [4]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests normal arrays of scores (Question type 1)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType1SimilarVsDifferent()
    {
        $smallerDistance = Algorithm::questionType1Distance([1, 2, 3, 4], [1, 3, 3, 4]);
        $largerDistance = Algorithm::questionType1Distance([1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests long arrays of scores (Question type 1)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType1SimilarVsDifferentLong()
    {
        $smallerDistance = Algorithm::questionType1Distance([1, 2, 3, 4, 1, 1, 3, 1, 3, 5, 3, 1], [1, 3, 3, 4, 1, 2, 3, 1, 3, 5, 3, 1]);
        $largerDistance = Algorithm::questionType1Distance([1, 2, 3, 4, 5, 5, 4, 3, 2, 1, 1, 1], [4, 3, 2, 1, 1, 1, 3, 1, 2, 3, 3, 5]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests single-valued arrays of scores (Question type 1)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType1SimilarVsDifferentShort()
    {
        $smallerDistance = Algorithm::questionType1Distance([1], [2]);
        $largerDistance = Algorithm::questionType1Distance([1], [4]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is exactly 3.
     */
    public function testType1ExactDistance1()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 5], [1, 2, 3, 4, 2]);
        $this->assertEquals(3, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is exactly 1.
     */
    public function testType1ExactDistance2()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4], [1, 3, 3, 4]);
        $this->assertEquals(1, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is very close to 4.472135955 (rounding).
     */
    public function testType1ExactDistance3()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(4.472135955, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is exactly 3.
     */
    public function testType1ExactDistance4()
    {
        $distance = Algorithm::questionType1Distance([1], [4]);
        $this->assertEquals(3, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is exactly 1.
     */
    public function testType1ExactDistance5()
    {
        $distance = Algorithm::questionType1Distance([1], [2]);
        $this->assertEquals(1, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is very close to 1.414213562 (account for rounding).
     */
    public function testType1ExactDistance6()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 1, 1, 3, 1, 3, 5, 3, 1], [1, 3, 3, 4, 1, 2, 3, 1, 3, 5, 3, 1]);
        $this->assertEquals(1.414213562, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 1).
     * Ensures that the distance between them is exactly 9.
     */
    public function testType1ExactDistance7()
    {
        $distance = Algorithm::questionType1Distance([1, 2, 3, 4, 5, 5, 4, 3, 2, 1, 1, 1], [4, 3, 2, 1, 1, 1, 3, 1, 2, 3, 3, 5]);
        $this->assertEquals(9, $distance);
    }




    /*
     *  Question Type 2 Tests
     */


    /**
     * Tests normal arrays of rankings (Question type 2)
     * Takes two arrays with the same values.
     * Ensures that the distance between them is 0.
     */
    public function testType2ZeroDistance()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 2, 3, 4, 5, 6]);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests normal arrays of rankings (Question type 2)
     * Takes two arrays with different values.
     * Ensures that the distance between them is not 0.
     */
    public function testType2NotZeroDistance()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 6, 3, 4, 2, 5]);
        $this->assertGreaterThan(0, $distance);
    }


    /**
     * Tests that the distance between arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceSimpleArrays1()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 3, 2, 4, 5, 6]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceSimpleArrays2()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [5, 6, 4, 3, 2, 1]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between long arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceLongArrays1()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [1, 2, 3, 4, 5, 8, 7, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between long arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceLongArrays2()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [10, 7, 23, 20, 5, 6, 2, 18, 19, 1, 11, 25, 4, 14, 15, 16, 17, 8, 9, 13, 21, 22, 3, 24, 12]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between short arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceShortArrays1()
    {
        $distance = Algorithm::questionType2Distance([1], [2]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between short arrays of rankings is positive (Question type 2).
     */
    public function testType2NotNegativeDistanceShortArrays2()
    {
        $distance = Algorithm::questionType2Distance([1], [3]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests normal arrays of rankings (Question type 2)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType2SimilarVsDifferent()
    {
        $smallerDistance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 3, 2, 4, 5, 6]);
        $largerDistance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [5, 6, 4, 3, 2, 1]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests longer arrays of rankings (Question type 2)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType2SimilarVsDifferentLong()
    {
        $smallerDistance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [1, 2, 3, 4, 5, 8, 7, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]);
        $largerDistance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [10, 7, 23, 20, 5, 6, 2, 18, 19, 1, 11, 25, 4, 14, 15, 16, 17, 8, 9, 13, 21, 22, 3, 24, 12]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests shorter arrays of rankings (Question type 2)
     * Takes two arrays that are very similar, and two that are very different.
     * Ensures that the distance is smaller between the more similar arrays.
     */
    public function testType2SimilarVsDifferentShort()
    {
        $smallerDistance = Algorithm::questionType2Distance([1], [2]);
        $largerDistance = Algorithm::questionType2Distance([1], [3]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    public function testType2ExactDistance1()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 6, 3, 4, 2, 5]);
        $this->assertEquals(5.099019514, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 2).
     * Ensures that the distance between them is very close to 1.41421356 (rounding).
     */
    public function testType2ExactDistance2()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6], [1, 3, 2, 4, 5, 6]);
        $this->assertEquals(1.41421356, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 2).
     * Ensures that the distance between them is very close to 2.828427125 (rounding).
     */
    public function testType2ExactDistance3()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [1, 2, 3, 4, 5, 8, 7, 6, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]);
        $this->assertEquals(2.828427125, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 2).
     * Ensures that the distance between them is very close to 46.216880033 (rounding).
     */
    public function testType2ExactDistance4()
    {
        $distance = Algorithm::questionType2Distance([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25], [10, 7, 23, 20, 5, 6, 2, 18, 19, 1, 11, 25, 4, 14, 15, 16, 17, 8, 9, 13, 21, 22, 3, 24, 12]);
        $this->assertEquals(46.216880033, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 2).
     * Ensures that the distance between them is exactly 2.
     */
    public function testType2ExactDistance5()
    {
        $distance = Algorithm::questionType2Distance([1], [3]);
        $this->assertEquals(2, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 2).
     * Ensures that the distance between them is exactly 10.
     */
    public function testType2ExactDistance6()
    {
        $distance = Algorithm::questionType2Distance([1], [11]);
        $this->assertEquals(10, $distance);
    }

    /*
     *  Question Type 3 Tests
     */

    /**
     * Tests distance between two equal points on a map (Question type 3).
     * Takes two arrays of a [lat,long] that represent the same point.
     * Ensures that the distance between them is zero.
     */
    public function testType3ZeroDistance()
    {
        $distance = Algorithm::questionType3Distance([30.0497079, 60.3336572], [30.0497079, 60.3336572]);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests distance between two distinct points on a map (Question type 3).
     * Takes two arrays of a [lat,long] that do not represent the same location.
     * Ensures that the distance between them is not 0.
     */
    public function testType3NotZeroDistance()
    {
        $distance = Algorithm::questionType3Distance([150.0496089, -70.3336572], [30.0497079, 60.3336572]);
        $this->assertGreaterThan(0, $distance);
    }


    /**
     * Tests that the distance between India and Pakistan is positive (Question type 3).
     */
    public function testType3NotNegativeDistanceIndiaPakistan()
    {
        $distance = Algorithm::questionType3Distance([20.0121253, 64.446581], [30.0497079, 60.3336572]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between India and Sweden is positive (Question type 3).
     */
    public function testType3NotNegativeDistanceIndiaSweden()
    {
        $distance = Algorithm::questionType3Distance([20.0121253, 64.446581], [61.7420487, 8.4405546]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between two Pacific Ocean points is positive (Question type 3).
     */
    public function testType3NotNegativeDistancePacificOcean()
    {
        $distance = Algorithm::questionType3Distance([11.47837, 156.8949893], [11.4898009, 152.4098799]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between Pacific and Arctic Oceans is positive (Question type 3).
     */
    public function testType3NotNegativeDistancePacificArcticOcean()
    {
        $distance = Algorithm::questionType3Distance([11.47837, 156.8949893], [65.351167, -59.3491597]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests distance between points on map (Question type 3)
     * Takes two arrays of a [lat,long] that are closer, and two that are farther away.
     * Ensures that the distance is smaller between the nearer coordinates.
     */
    public function testType3CloseVsFar()
    {
        //India vs Pakistan
        $closer = Algorithm::questionType3Distance([20.0121253, 64.446581], [30.0497079, 60.3336572]);

        //India vs Sweden
        $farther = Algorithm::questionType3Distance([20.0121253, 64.446581], [61.7420487, 8.4405546]);
        $this->assertGreaterThan($closer, $farther);
    }

    /**
     * Tests distance between points on map (Question type 3)
     * Takes two arrays of a [lat,long] that are closer, and two that are farther away.
     * Ensures that the distance is smaller between the nearer coordinates.
     */
    public function testType3CloseVsFarOcean()
    {
        //Two points in the Pacific Ocean (fairly close together)
        $closer = Algorithm::questionType3Distance([11.47837, 156.8949893], [11.4898009, 152.4098799]);

        //A point in the Pacific Ocean vs a point in the Arctic Ocean
        $farther = Algorithm::questionType3Distance([11.47837, 156.8949893], [65.351167, -59.3491597]);

        $this->assertGreaterThan($closer, $farther);
    }


    /**
     * Tests distance between two points (Question type 3).
     * Ensures that the distance between India and Pakistan is very close to 1190.25833601 (rounding).
     */
    public function testType3ExactDistance1()
    {
        $distance = Algorithm::questionType3Distance([20.0121253, 64.446581], [30.0497079, 60.3336572]);
        $this->assertEquals(1190.25833601, $distance, '', 0.5);
    }

    /**
     * Tests distance between two points (Question type 3).
     * Ensures that the distance between India and Sweden is very close to 6296.10796338 (rounding).
     */
    public function testType3ExactDistance2()
    {
        $distance = Algorithm::questionType3Distance([20.0121253, 64.446581], [61.7420487, 8.4405546]);
        $this->assertEquals(6296.10796338, $distance, '', 3);
    }

    /**
     * Tests distance between two points (Question type 3).
     * Ensures that the distance between 2 pacific ocean points is very close to 488.733732416 (rounding).
     */
    public function testType3ExactDistance3()
    {
        $distance = Algorithm::questionType3Distance([11.47837, 156.8949893], [11.4898009, 152.4098799]);
        $this->assertEquals(488.733732416, $distance, '', 0.5);
    }

    /**
     * Tests distance between two points (Question type 3).
     * Ensures that the distance the pacfiic and arctic oceans is very close to 10958.8547038 (rounding).
     */
    public function testType3ExactDistance4()
    {
        $distance = Algorithm::questionType3Distance([11.47837, 156.8949893], [65.351167, -59.3491597]);
        $this->assertEquals(10958.8547038, $distance, '', 4);
    }

    /*
     *  Question Type 4 Tests
     */

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes two equal numbers (can be float) and compares how far apart they are.
     * Ensures that the distance between them is zero.
     */
    public function testType4ZeroDistance()
    {
        $distance = Algorithm::questionType4Distance(2, 2);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes two different numbers (can be float) and compares how far apart they are.
     * Ensures that the distance between them is not zero.
     */
    public function testType4NotZeroDistance()
    {
        $distance = Algorithm::questionType4Distance(4, 2);
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Tests that the distance between positive numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistancePositiveNumbers1()
    {
        $distance = Algorithm::questionType4Distance(16, 24);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between positive numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistancePositiveNumbers2()
    {
        $distance = Algorithm::questionType4Distance(67, 1800);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between negative numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistanceNegativeNumbers1()
    {
        $distance = Algorithm::questionType4Distance(-16, -24);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between negative numbers is positive (Question type 4).
     */
    public function testType1NotNegativeDistanceNegativeNumbers2()
    {
        $distance = Algorithm::questionType4Distance(-67, -18);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between a positive and a negative number is positive (Question type 4).
     */
    public function testType4NotNegativeDistancePosAndNegNumbers1()
    {
        $distance = Algorithm::questionType4Distance(-16, 0);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between a positive and a negative number is positive (Question type 4).
     */
    public function testType4NotNegativeDistancePosAndNegNumbers2()
    {
        $distance = Algorithm::questionType4Distance(-67, 2);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistanceDecimalNumbers1()
    {
        $distance = Algorithm::questionType4Distance(1.1567, 1.1568);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistanceDecimalNumbers2()
    {
        $distance = Algorithm::questionType4Distance(4.001, 4.003);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistanceDecimalNumbers3()
    {
        $distance = Algorithm::questionType4Distance(-1.1567, -1.1568);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 4).
     */
    public function testType4NotNegativeDistanceDecimalNumbers4()
    {
        $distance = Algorithm::questionType4Distance(-4.001, 4.003);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes two numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calculated for the closer numbers is less than for the further apart numbers.
     */
    public function testType4Distance()
    {
        $smallerDistance = Algorithm::questionType4Distance(16, 24);
        $largerDistance = Algorithm::questionType4Distance(67, 18);

        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes two negative numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the closer numbers is less than for the further apart numbers.
     */
    public function testType4DistanceNegatives()
    {
        $smallerDistance = Algorithm::questionType4Distance(-16, -24);
        $largerDistance = Algorithm::questionType4Distance(-67, -18);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes a positive and a negative number (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the closer numbers is less than for the further apart numbers.
     */
    public function testType4DistanceNegativeAndPositive()
    {
        $smallerDistance = Algorithm::questionType4Distance(-16, 0);
        $largerDistance = Algorithm::questionType4Distance(-67, 2);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes 2 numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is equal, as they are
     * equally far away despite being different.
     */
    public function testType4DistanceEqualDistances()
    {
        $distance1 = Algorithm::questionType4Distance(-2, 0);
        $distance2 = Algorithm::questionType4Distance(4, 6);
        $this->assertEquals($distance1, $distance2);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes 2 numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is not equal.
     */
    public function testType4DistanceNotEqualDistances()
    {
        $smallerDistance = Algorithm::questionType4Distance(-2, 0);
        $largerDistance = Algorithm::questionType4Distance(4, 7);
        $this->assertNotEquals($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 4).
     * Takes 2 extremely close float numbers and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is not equal, as they are
     * not equally spaced apart, due to fractions of a decimal.
     */
    public function testType4DistanceNotEqualDistancesFloat()
    {
        $smallerDistance = Algorithm::questionType4Distance(1.1567, 1.1568);  //0.0001 apart
        $largerDistance = Algorithm::questionType4Distance(4.001, 4.003);    //0.0002 apart
        $this->assertNotEquals($smallerDistance, $largerDistance);
    }


    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 8.
     */
    public function testType4ExactDistance1()
    {
        $distance = Algorithm::questionType4Distance(16, 24);
        $this->assertEquals(8, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 49.
     */
    public function testType4ExactDistance2()
    {
        $distance = Algorithm::questionType4Distance(-67, -18);
        $this->assertEquals(49, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 0.001.
     */
    public function testType4ExactDistance3()
    {
        $distance = Algorithm::questionType4Distance(1.1567, 1.1568);
        $this->assertEquals(0.0001, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 0.0001.
     */
    public function testType4ExactDistance4()
    {
        $distance = Algorithm::questionType4Distance(-1.1567, -1.1568);
        $this->assertEquals(0.0001, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 8.004.
     */
    public function testType4ExactDistance5()
    {
        $distance = Algorithm::questionType4Distance(-4.001, 4.003);
        $this->assertEquals(8.004, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 4).
     * Ensures that the distance (absolute value) is exactly 69.
     */
    public function testType4ExactDistance6()
    {
        $distance = Algorithm::questionType4Distance(-67, 2);
        $this->assertEquals(69, $distance);
    }

    /*
     *  Question Type 5 Tests
     *  Test that deprecate function calls returns error as expected
     */

    /**
     ** @Test
     * @expectedException \ErrorException
     * @expectedExceptionMessage Deprecated function called.
     */
    public function testType5DeprecatedFunctionBetweenTwoCloseDates()
    {
        $distance = Algorithm::questionType5Distance('2019-01-19', '2019-01-15');
        $this->assertEquals(4, $distance);
    }

    /**
     ** @Test
     * @expectedException \ErrorException
     * @expectedExceptionMessage Deprecated function called.
     */
    public function testType5DeprecatedFunctionBetweenTwoFarDates()
    {
        $distance = Algorithm::questionType5Distance('2019-01-19', '2019-05-15');
        $this->assertEquals(117, $distance);
    }


    /*
     *  Question Type 6 Tests
     */

    /**
     * Tests normal arrays of selections (Question type 6)
     * Takes two arrays with the same values.
     * Ensures that the distance between them is 0.
     */
    public function testType6ZeroDistance()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0], [0, 0, 0, 0, 0]);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests normal arrays of selections (Question type 6)
     * Takes two arrays with different values.
     * Ensures that the distance between them is not 0.
     */
    public function testType6NotZeroDistance()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0], [0, 0, 0, 1, 0]);
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Tests that the distance between arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceSimpleArrays1()
    {
        $distance = Algorithm::questionType6Distance([0, 1, 0, 1], [0, 1, 1, 1]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceSimpleArrays2()
    {
        $distance = Algorithm::questionType6Distance([1, 1, 1, 1], [0, 0, 0, 0]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between long arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceLongArrays1()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
        $this->assertGreaterThanOrEqual(0, $distance);

    }

    /**
     * Tests that the distance between long arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceLongArrays2()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between short arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceShortArrays1()
    {
        $distance = Algorithm::questionType6Distance([0], [0]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between short arrays of selections is positive (Question type 6).
     */
    public function testType6NotNegativeDistanceShortArrays2()
    {
        $distance = Algorithm::questionType6Distance([0], [1]);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests difference between two arrays (Question type 6).
     * Takes 2 extremely similar arrays, and then 2 somewhat different arrays, and compares each pair.
     * Ensures that the distance calcualated for the different arrays is larger than the one calculated
     * for the very similar arrays.
     */
    public function testType6Distance()
    {
        $smallerDistance = Algorithm::questionType6Distance([0, 1, 0, 1], [0, 1, 1, 1]);
        $largerDistance = Algorithm::questionType6Distance([1, 1, 1, 1], [0, 0, 0, 0]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests difference between two single-value arrays (Question type 6).
     * Takes 2 extremely similar arrays, and then 2 somewhat different arrays, and compares each pair.
     * Ensures that the distance calcualated for the different arrays is larger than the one calculated
     * for the very similar arrays.
     */
    public function testType6DistanceSingleValue()
    {
        $smallerDistance = Algorithm::questionType6Distance([0], [0]);
        $largerDistance = Algorithm::questionType6Distance([0], [1]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests difference between two longer arrays (Question type 6).
     * Takes 2 extremely similar arrays, and then 2 somewhat different arrays, and compares each pair.
     * Ensures that the distance calcualated for the different arrays is larger than the one calculated
     * for the very similar arrays.
     */
    public function testType6DistanceLong()
    {
        $smallerDistance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
        $largerDistance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1]);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is exactly 1.
     */
    public function testType6ExactDistance1()
    {
        $distance = Algorithm::questionType6Distance([0, 1, 0, 1], [0, 1, 1, 1]);
        $this->assertEquals(1, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is exactly 2.
     */
    public function testType6ExactDistance2()
    {
        $distance = Algorithm::questionType6Distance([1, 1, 1, 1], [0, 0, 0, 0]);
        $this->assertEquals(2, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is exactly 1.
     */
    public function testType6ExactDistance3()
    {

        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
        $this->assertEquals(1, $distance);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is very close to 4.358898944 (rounding).
     */
    public function testType6ExactDistance4()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1]);
        $this->assertEquals(4.358898944, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is very close to 4.123105625 (rounding).
     */
    public function testType6ExactDistance5()
    {
        $distance = Algorithm::questionType6Distance([0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1]);
        $this->assertEquals(4.123105625, $distance, '', 0.00000001);
    }

    /**
     * Tests normal arrays of scores (Question type 6).
     * Ensures that the distance between them is exactly 1.
     */
    public function testType6ExactDistance6()
    {
        $distance = Algorithm::questionType6Distance([0], [1]);
        $this->assertEquals(1, $distance, '', 0.00000001);
    }



    /*
     *  Question Type 7 Tests
     */

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes two equal numbers (can be float) and compares how far apart they are.
     * Ensures that the distance between them is zero.
     */
    public function testType7ZeroDistance()
    {
        $distance = Algorithm::questionType7Distance(20000, 20000);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes two different numbers (can be float) and compares how far apart they are.
     * Ensures that the distance between them is not zero.
     */
    public function testType7NotZeroDistance()
    {
        $distance = Algorithm::questionType7Distance(-400, 22);
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Tests that the distance between positive numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistancePositiveNumbers1()
    {
        $distance = Algorithm::questionType7Distance(160, 240);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between positive numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistancePositiveNumbers2()
    {
        $distance = Algorithm::questionType7Distance(67000, 18);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between negative numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceNegativeNumbers1()
    {
        $distance = Algorithm::questionType7Distance(-16, -24);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between negative numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceNegativeNumbers2()
    {
        $distance = Algorithm::questionType7Distance(-67, -18);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between a negative and positive number is positive (Question type 7).
     */
    public function testType7NotNegativeDistancePosAndNegNumbers1()
    {
        $distance = Algorithm::questionType7Distance(-160, 0);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between a negative and positive number is positive (Question type 7).
     */
    public function testType7NotNegativeDistancePosAndNegNumbers2()
    {
        $distance = Algorithm::questionType7Distance(-6700, 20);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceDecimalNumbers1()
    {
        $distance = Algorithm::questionType7Distance(-2.00, 0.03);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceDecimalNumbers2()
    {
        $distance = Algorithm::questionType7Distance(4.03, 6.06);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceDecimalNumbers3()
    {
        $distance = Algorithm::questionType7Distance(-1.1567, -1.1568);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between decimal numbers is positive (Question type 7).
     */
    public function testType7NotNegativeDistanceDecimalNumbers4()
    {
        $distance = Algorithm::questionType7Distance(-4.001, 4.003);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes two numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the closer numbers is less than for the further apart numbers.
     */
    public function testType7Distance()
    {
        $smallerDistance = Algorithm::questionType7Distance(160, 240);
        $largerDistance = Algorithm::questionType7Distance(67000, 18);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes two negative numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the closer numbers is less than for the further apart numbers.
     */
    public function testType7DistanceNegatives()
    {
        $smallerDistance = Algorithm::questionType7Distance(-16, -24);
        $largerDistance = Algorithm::questionType7Distance(-67, -18);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes a positive and a negative number (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the closer numbers is less than for the further apart numbers.
     */
    public function testType7DistanceNegativeAndPositive()
    {
        $smallerDistance = Algorithm::questionType7Distance(-160, 0);
        $largerDistance = Algorithm::questionType7Distance(-6700, 20);
        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes 2 numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is equal, as they are
     * equally far away despite being different.
     */
    public function testType7DistanceEqualDistances()
    {
        $distance1 = Algorithm::questionType7Distance(-2.00, 0.03);
        $distance2 = Algorithm::questionType7Distance(4.03, 6.06);
        $this->assertEquals($distance1, $distance2);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes 2 numbers (can be float) and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is not equal.
     */
    public function testType7DistanceNotEqualDistances()
    {
        $smallerDistance = Algorithm::questionType7Distance(-2, -55);
        $largerDistance = Algorithm::questionType7Distance(400, 79);
        $this->assertNotEquals($smallerDistance, $largerDistance);
    }

    /**
     * Tests absolute value between two numbers (Question type 7).
     * Takes 2 extremely close float numbers and compares how far apart they are.
     * Ensures that the distance calcualated for the numbers is not equal, as they are
     * not equally spaced apart, due to fractions of a decimal.
     */
    public function testType7DistanceNotEqualDistancesFloat()
    {
        $smallerDistance = Algorithm::questionType7Distance(101.1567, 101.1568);  //0.0001 apart
        $largerDistance = Algorithm::questionType7Distance(-4.001, -4.003);    //0.0002 apart
        $this->assertNotEquals($smallerDistance, $largerDistance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 160.
     */
    public function testType7ExactDistance1()
    {
        $distance = Algorithm::questionType7Distance(160, 0);
        $this->assertEquals(160, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 113.
     */
    public function testType7ExactDistance2()
    {
        $distance = Algorithm::questionType7Distance(-67, -180);
        $this->assertEquals(113, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 0.001.
     */
    public function testType7ExactDistance3()
    {
        $distance = Algorithm::questionType7Distance(101.1567, 101.1568);
        $this->assertEquals(0.0001, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 0.0001.
     */
    public function testType7ExactDistance4()
    {
        $distance = Algorithm::questionType7Distance(-1.1567, -1.1568);
        $this->assertEquals(0.0001, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 0.002.
     */
    public function testType7ExactDistance5()
    {
        $distance = Algorithm::questionType7Distance(-4.001, -4.003);
        $this->assertEquals(0.002, $distance);
    }

    /**
     * Tests distance between two numbers (Question type 7).
     * Ensures that the distance (absolute value) is exactly 6720.
     */
    public function testType7ExactDistance6()
    {
        $distance = Algorithm::questionType7Distance(-6700, 20);
        $this->assertEquals(6720, $distance);
    }

    /*
     *  Question Type 8 Tests
     *
     */

    /**
     * Tests distance between two colors (Question type 7).
     * Takes two equal colors and compares how far apart they are.
     * Ensures that the distance between them is zero.
     */
    public function testType8ZeroDistance()
    {
        $distance = Algorithm::questionType8Distance(['#FFFFFF'], ['#FFFFFF']);
        $this->assertEquals(0, $distance);
    }

    /**
     * Tests distance between two colors (Question type 7).
     * Takes two distinct colors and compares how far apart they are.
     * Ensures that the distance between them is not zero.
     */
    public function testType8NotZeroDistance()
    {
        $distance = Algorithm::questionType8Distance(['#FFFFFF'], ['#F0FFFF']);
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Tests that the distance between red and yellow is positive (Question type 8).
     */
    public function testType8NotNegativeDistanceRedVsYellow()
    {
        $distance = Algorithm::questionType8Distance(['#fa0000'], ['#fafa00']);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between black and white is positive (Question type 8).
     */
    public function testType8NotNegativeDistanceBlackVsWhite()
    {
        $distance = Algorithm::questionType8Distance(['#FFFFFF'], ['#000000']);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between dark green and darker green is positive (Question type 8).
     */
    public function testType8NotNegativeDistanceSimilarGreens()
    {
        $distance = Algorithm::questionType8Distance(['#003c00'], ['#006100']);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests that the distance between dark green and bright green is positive (Question type 8).
     */
    public function testType8NotNegativeDistanceDifferentGreens()
    {
        $distance = Algorithm::questionType8Distance(['#003c00'], ['#00fb00']);
        $this->assertGreaterThanOrEqual(0, $distance);
    }

    /**
     * Tests distance between two colors (Question type 8).
     * Takes two distinct colors and compares how far apart they are.
     * Ensures that the distance between the more similar colors is less than the distance
     * between the more equal colors.
     */
    public function testType8Distance()
    {
        //Red vs Yellow (closer)
        $smallerDistance = Algorithm::questionType8Distance(['#fa0000'], ['#fafa00']);

        //Black vs White (further apart)
        $largerDistance = Algorithm::questionType8Distance(['#FFFFFF'], ['#000000']);

        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests distance between two colors (Question type 7).
     * Takes two distinct colors and compares how far apart they are.
     * Ensures that the distance between the more similar colors is less than the distance
     * between the more equal colors.
     */
    public function testType8DistanceShadesOfSameColor()
    {
        //Very Dark Green vs Dark Green (closer)
        $smallerDistance = Algorithm::questionType8Distance(['#003c00'], ['#006100']);

        //Very Dark Green vs Bright Green (further apart)
        $largerDistance = Algorithm::questionType8Distance(['#003c00'], ['#00fb00']);

        $this->assertGreaterThan($smallerDistance, $largerDistance);
    }

    /**
     * Tests distance between two colors (Question type 8).
     * Ensures that the distance between red and yellow is exactly 250.
     */
    public function testType8ExactDistance1()
    {
        $distance = Algorithm::questionType8Distance(['#fa0000'], ['#fafa00']);
        $this->assertEquals(250, $distance);
    }

    /**
     * Tests distance between two colors (Question type 8).
     * Ensures that the distance between black and white is very close to 441.6729559 (rounding).
     */
    public function testType8ExactDistance2()
    {
        $distance = Algorithm::questionType8Distance(['#FFFFFF'], ['#000000']);
        $this->assertEquals(441.6729559, $distance, '', 0.000001);
    }

    /**
     * Tests distance between two colors (Question type 8).
     * Ensures that the distance between dark green and very dark green is exactly 37.
     */
    public function testType8ExactDistance3()
    {
        $distance = Algorithm::questionType8Distance(['#003c00'], ['#006100']);
        $this->assertEquals(37, $distance);
    }

    /**
     * Tests distance between two colors (Question type 8).
     * Ensures that the distance between dark green and bright green is exactly 191.
     */
    public function testType8ExactDistance4()
    {
        $distance = Algorithm::questionType8Distance(['#003c00'], ['#00fb00']);
        $this->assertEquals(191, $distance, '', 0.00000001);
    }


    public function testSpecialDistanceForNegativeTypeTwo()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->specialDistance(0, -2, $user1, $user2, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(1000, $result);
    }

    public function testSpecialDistanceForNegativeTypeOne()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->specialDistance(0, -1, $user1, $user2, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(1000000, $result);
    }


    /**
     ** @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Special question with ID 0 has unknown type 1
     */
    public function testSpecialDistanceThrowsExpectedException()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->specialDistance(0, 1, $user1, $user2, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(1000000000000, $result);
    }

    /**
     ** @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Special question with ID 0 has unknown type -11
     */
    public function testSpecialDistanceThrowsExpectedExceptionWithNegativeQuestionTypeID()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->specialDistance(0, -11, $user1, $user2, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(10000, $result);
    }

    public function testAnswerDistanceQuestionIDOne()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 1
        $result = $algorithm->answerDistance(1, 1, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(0.5, $result, '', 0.000001);
    }

    public function testAnswerDistanceQuestionIDTwo()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 2
        $result = $algorithm->answerDistance(2, 2, [1, 2, 3, 4, 1, 1, 3, 1, 3, 5, 3, 1], [1, 3, 3, 4, 1, 2, 3, 1, 3, 5, 3, 1]);
        $this->assertEquals(0.1690308509457, $result);

    }

    public function testAnswerDistanceQuestionIDThree()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 3
        $result = $algorithm->answerDistance(3, 3, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(0.017571850, $result, '', 0.000001);
    }

    public function testAnswerDistanceQuestionIDFour()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 4
        $result = $algorithm->answerDistance(4, 4, [22], [19]);
        $this->assertEquals(0.008219178, $result, '', 0.0000000001);
    }

    /**
     ** @Test
     * @expectedException \ErrorException
     * @expectedExceptionMessage Deprecated function called.
     */
    public function testAnswerDistanceQuestionIDFiveCheckDeprecatedFunctionCalled()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 5
        $result = $algorithm->answerDistance(5, 5, ['2019-09-10'], ['2019-09-20']);
        $this->assertEquals(0, $result, '', 0.000001);
    }


    public function testAnswerDistanceQuestionIDSix()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8, 'validation' => ['options' => 4]],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 6
        $result = $algorithm->answerDistance(6, 8, [0, 1, 1, 1], [0, 1, 1, 1]);
        $this->assertEquals(0, $result);
    }

    public function testAnswerDistanceQuestionIDSeven()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type ID case 4
        $result = $algorithm->answerDistance(4, 7, [22], [19]);
        $this->assertEquals(0.025, $result);
    }

    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID 9 has unknown type 9
     */
    public function testAnswerDistanceThrowsExpectedExceptionWhenCaseStatementNotReached()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0, 8 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->answerDistance(9, 9, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(2, $result);
    }

    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID -2 has unknown type -2
     */
    public function testAnswerThrowsExpectedExceptionWithNegativeQuestionID()
    {
        $user1 = new User();
        $user1->gender = 1; //user1 is a male
        $user1->id = 1;
        $user2 = new User();
        $user2->gender = 2; //user2 is a female
        $user2->id = 2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme, [
            0 => ['type_id' => -1],
            1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3 => ['type_id' => 3],
            4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
            5 => ['type_id' => 6, 'validation' => ['options' => 10]],
            6 => ['type_id' => 8],
            7 => ['type_id' => -2]],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0, 8 => 1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->answerDistance(-2, -2, [1, 2, 3, 4], [4, 3, 2, 1]);
        $this->assertEquals(2, $result);
    }
}