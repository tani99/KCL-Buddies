<?php

namespace Tests\Unit\Algorithm;
use Tests\TestCase;
use App\Algorithm;
use App\Scheme;
use App\User;
use Mockery as Mockery;

class AlgorithmTest extends TestCase
{
    public function testCreateInitialMappingWithOneKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1],[1]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1]], 1=>[0=>[0=>1]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithTwoKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1,0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2],[1,6]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2]], 1=>[0=>[0=>1, 1=>6]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithThreeKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3],[1,6,9]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3]], 1=>[0=>[0=>1, 1=>6, 2=>9]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithFourKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3,4],[1,6,9,2]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3, 3=>4]], 1=>[0=>[0=>1, 1=>6, 2=>9, 3=>2]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithFiveKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3,4,5],[1,6,9,2,7]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3, 3=>4, 4=>5]], 1=>[0=>[0=>1, 1=>6, 2=>9, 3=>2, 4=>7]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithSixKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3,4,5,6],[1,6,9,2,7,3]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3, 3=>4, 4=>5, 5=>6]], 1=>[0=>[0=>1, 1=>6, 2=>9, 3=>2, 4=>7, 5=>3]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithSevenKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3,4,5,6,7],[1,6,9,2,7,3,4]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7]], 1=>[0=>[0=>1, 1=>6, 2=>9, 3=>2, 4=>7, 5=>3, 6=>4]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testCreateInitialMappingWithEightKeysAndValues()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->createInitialMapping([[1,2,3,4,5,6,7,8],[1,6,9,2,7,3,4,5]]);
        $this->assertEquals([0=>[0=>[0=>[0=>1, 1=>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7, 7=>8]], 1=>[0=>[0=>1, 1=>6, 2=>9, 3=>2, 4=>7, 5=>3, 6=>4, 7=>5]]], 1=>[0=>[], 1=>[]]], $result);
    }

    public function testVectorMagnitudeWithFourProducesCorrectMagnitude()
    {
        $vectorArray = [1, 2, 3, 4];
        $result = Algorithm::vectorMagnitude($vectorArray);
        $this->assertEquals(5.4772, $result, '', 0.0001);
    }

    public function testVectorMagnitudeWithZeroProducesCorrectMagnitude()
    {
        $vectorArray1 = [0, 0, 0, 0];
        $result1 = Algorithm::vectorMagnitude($vectorArray1);
        $this->assertEquals(0, $result1);
    }

    public function testVectorMagnitudeWithNineProducesCorrectMagnitude()
    {
        $vectorArray2 = [0, 3, 4, 8, 7, 4, 5, 6, 9];
        $result2 = Algorithm::vectorMagnitude($vectorArray2);
        $this->assertEquals(17.2046, $result2, '', 0.0001);
    }

    public function testGetNormalisationValueForNegativeTypeTwo()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case -2
        $result = $algorithm->getNormalisationValue(7, -2);
        $this->assertEquals(1, $result);
    }


    public function testGetNormalisationValueForNegativeTypeOne()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case -1
        $result1 =$algorithm->getNormalisationValue( 0, -1);
        $this->assertEquals(1, $result1);
    }


    public function testGetNormalisationValueForTypeOne()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 1
        $result2 = $algorithm->getNormalisationValue( 1, 1);
        $this->assertEquals(8.9442719099992, $result2);
    }

    public function testGetNormalisationValueForTypeTwo()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 2
        $result2 = $algorithm->getNormalisationValue(2 , 2);
        $this->assertEquals(8.3666002653408, $result2);
    }

    public function testGetNormalisationValueForTypeThree()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 3
        $result2 = $algorithm->getNormalisationValue(3 , 3);
        $this->assertEquals(20015.086796, $result2);
    }

    public function testGetNormalisationValueForTypeFour()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 4
        $result2 = $algorithm->getNormalisationValue(4 , 4);
        $this->assertEquals(365, $result2);
    }
    public function testGetNormalisationValueForTypeFive()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2],
            8=>['type_id'=>5, 'validation' => ['min'=>0, 'max'=>120]],
            ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 5
        $result2 = $algorithm->getNormalisationValue(8 , 5);
        $this->assertEquals(120, $result2);
    }


    public function testGetNormalisationValueForTypeSix()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['max' => 5, 'min'=>2, 'options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 6
        $result2 = $algorithm->getNormalisationValue(5 , 6);
        $this->assertEquals(10, $result2);
    }

    public function testGetNormalisationValueForTypeSeven()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 7
        $result2 = $algorithm->getNormalisationValue(4 , 7);
        $this->assertEquals(120, $result2);
    }

    public function testGetNormalisationValueForTypeEight()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 8
        $result2 = $algorithm->getNormalisationValue(6 , 8);
        $this->assertEquals(441.67295593006, $result2);
    }

    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID 3 has unknown type 9
     */
    public function testGetNormalisationValueThrowsExpectedException()
    {

        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->getNormalisationValue(3, 9);
        $this->assertEquals(8, $result);
    }

    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID 4 has unknown type 13
     */
    public function testGetNormalisationValueThrowsExpectedExceptionWithNegativeQuestionID()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->getNormalisationValue( 4, 13);
        $this->assertEquals(7, $result);
    }

    /**
     * Test distance weight for question with question ID -2
     * The question weighting of question ID 0
     * is set to a high number (999)  to ensure correct distance
     * weight is returned
     *
     */
    public function testGetDistanceWeightForTypeNegativeTwo()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case -2
        $result = $algorithm->getDistanceWeight(7, -2);
        $this->assertEquals(10, $result);
    }


    /**
     * Test distance weight for question with question ID -1
     * The question weighting of question ID 1
     * is set to a high number (50) to ensure correct distance
     * weight is returned
     *
     */
    public function testGetDistanceWeightForTypeNegativeOne()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case -1
        $result1 = $algorithm->getDistanceWeight(0, -1);
        $this->assertEquals(1000000, $result1);
    }

    /**
     * Test distance weight for question with question ID 1
     *
     */
    public function testGetDistanceWeightForType1()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 1
        $result2 = $algorithm->getDistanceWeight(1, 1);
        $this->assertEquals(1, $result2);
    }

    /**
     * Test distance weight for question with question ID 2
     *
     */
    public function testGetDistanceWeightForType2()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 =>['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 2.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 2
        $result3 = $algorithm->getDistanceWeight( 2, 2);
        $this->assertEquals(2, $result3);
    }

    /**
     * Test distance weight for question with question ID 3
     *
     */
    public function testGetDistanceWeightForType3()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 2.0, 3 => 15.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 3
        $result4 = $algorithm->getDistanceWeight(3, 3);
        $this->assertEquals(15, $result4);
    }


    /**
     * Test distance weight for question with question ID 4
     *
     */
    public function testGetDistanceWeightForType4()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 2.0, 3 => 1.0, 4=>13.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 4
        $result5 = $algorithm->getDistanceWeight( 4, 7);
        $this->assertEquals(13, $result5);
    }

    /**
     * Test distance weight for question with question ID 5
     *
     */
    public function testGetDistanceWeightForType5()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 5
        $result6 = $algorithm->getDistanceWeight( 5, 6);
        $this->assertEquals(1, $result6);
    }

    /**
     * Test distance weight for question with question ID 6
     *
     */
    public function testGetDistanceWeightForType6()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        //question type case 6
        $result7 = $algorithm->getDistanceWeight( 6, 8);
        $this->assertEquals(1, $result7);
    }

    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID 0 has unknown type -9
     */
    public function testGetDistanceWeightThrowsExpectedException()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->getDistanceWeight(0, -9);
        $this->assertEquals(2, $result);
    }


    /**
     * @Test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Question with ID -2 has unknown type 11
     */
    public function testGetDistanceWeightThrowsExpectedExceptionWithNegativeQuestionID()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->getDistanceWeight( -2, 11);
        $this->assertEquals(7, $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     * Small distances in array for each qs returned due to similarities in answers
     */
    public function testUserDistances1()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=256;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=257;
        $scheme = new Scheme();
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 2.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=> 1.0, 8=>1.0],
            [1 => [[$user1], [256 => null]], 2 => [[$user2], [257 => null]]]);
        $result = $algorithm->userDistances(
            [
                256 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[61.7420487,8.4405546],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#fafa00'],
                    7=>[0]
                ],
                257 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[6, 5 ,4, 3,1,2],
                    6=>['#fa0000'],
                    7=>[0]
                ]
            ], $user1, $user2);
        $this->assertEquals([  0 => 0.0,
                1 => 0.0,
                2 => 0.0,
                3 => 0.570826224268112358828375363373197615146636962890625,
                4 => 0.08333333333333332870740406406184774823486804962158203125,
                5 => 0.0,
                6 => 0.56602967567610840138314642899786122143268585205078125,
                7 => 0.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     * Small distances in array each qs returned due to similarities in answers
     * except for age and gender preference
     */
    public function testUserDistances2()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistances(
            [
                1 => [
                    0=>[1],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[0]
                ],
                2 => [
                    0=>[1],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#00fb00'],
                    7=>[1]
                ]
            ], $user1, $user2);
        $this->assertEquals([  0 => 1000000.0,
                1 => 0.0,
                2 => 0.0,
                3 => 0.024425932392628947609214407066247076727449893951416015625,
                4 => 0.08333333333333332870740406406184774823486804962158203125,
                5 => 0.0,
                6 => 0.432446672216546812439474933853489346802234649658203125,
                7 => 1000.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     */
    public function testUserDistances3()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=30;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=44;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [40 => null]], 2 => [[$user2], [44 => null]]]);
        $result = $algorithm->userDistances(
            [
                30 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[22],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#FFFFFF'],
                    7=>[0]
                ],
                44 => [
                    0=>[0],
                    1=>[ 2, 1, 0, -2, 1, -1],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[32],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#000000'],
                    7=>[1]
                ]
            ], $user1, $user2);
        $this->assertEquals([   0 => 0.0,
            1 => 0.6519202405202648709092727585812099277973175048828125,
            2 => 0.0,
            3 => 0.024425932392628947609214407066247076727449893951416015625,
            4 => 0.08333333333333332870740406406184774823486804962158203125,
            5 => 0.0,
            6 => 1.000000000000008437694987151189707219600677490234375,
            7 => 1000.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     * Distance identical for similar question answer
     */
    public function testUserDistances4()
    {
        $user1 = new User();
        $user1->gender=2; //user1 is a female
        $user1->id=19;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=20;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [19 => null]], 2 => [[$user2], [20 => null]]]);
        $result = $algorithm->userDistances(
            [
                19 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,6,4,2,5,3],
                    3=>[20.0121253, 64.446581],
                    4=>[18],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#003c00'],
                    7=>[0]
                ],
                20 => [
                    0=>[0],
                    1=>[ 2, 1, -1, 1, 0, -2],
                    2=>[1,6,4,2,5,3],
                    3=>[11.47837, 156.8949893],
                    4=>[17],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#006100'],
                    7=>[0]
                ]
            ], $user1, $user2);
        $this->assertEquals([      0 => 0.0,
                1 => 0.5244044240850758153982269504922442138195037841796875,
                2 => 0.0,
                3 => 0.4909971708784139909909072230220772325992584228515625,
                4 => 0.00833333333333333321768510160154619370587170124053955078125,
                5 => 0.0,
                6 => 0.08377239200006404706844165275470004417002201080322265625,
                7 => 0.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     */
    public function testUserDistances5()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=55;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=56;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [55 => null]], 2 => [[$user2], [56 => null]]]);
        $result = $algorithm->userDistances(
            [
                55 => [
                    0=>[0],
                    1=>[-2, 1, 1, 0, -1, 2],
                    2=>[4,1,2,3, 5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[18],
                    5=>[1, 4, 5, 6, 2, 3],
                    6=>['#003c00'],
                    7=>[0]
                ],
                56 => [
                    0=>[0],
                    1=>[ 2, -2, 1, -1, 1, 0,],
                    2=>[1,5,2,3,4,6],
                    3=>[11.47837, 156.8949893],
                    4=>[19],
                    5=>[1, 4, 5, 6, 3, 2],
                    6=>['#00fb00'],
                    7=>[0]
                ]
            ], $user1, $user2);
        $this->assertEquals([ 0 => 0.0,
                1 => 0.6519202405202648709092727585812099277973175048828125,
                2 => 0.60944940022004401303234999431879259645938873291015625,
                3 => 0.024425932392628947609214407066247076727449893951416015625,
                4 => 0.00833333333333333321768510160154619370587170124053955078125,
                5 => 0.0,
                6 => 0.432446672216546812439474933853489346802234649658203125,
                7 => 0.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     */
    public function testUserDistances6()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistances(
            [
                1 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[1]
                ],
                2 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#00fb00'],
                    7=>[1]
                ]
            ], $user1, $user2);
        $this->assertEquals([ 0 => 0.0,
                1 => 0.0,
                2 => 0.0,
                3 => 0.024425932392628947609214407066247076727449893951416015625,
                4 => 0.08333333333333332870740406406184774823486804962158203125,
                5 => 0.0,
                6 => 0.432446672216546812439474933853489346802234649658203125,
                7 => 1000.0], $result);
    }

    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     * Return 0 for all distances in array as results are identical
     */
    public function testUserDistances7()
    {
        $user1 = new User();
        $user1->gender=4; //user1 is a unspecified
        $user1->id=1;
        $user2 = new User();
        $user2->gender=4; //user2 is a unspecified
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->userDistances(
            [
                1 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[20.0121253, 64.446581],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[0]
                ],
                2 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[20.0121253, 64.446581],
                    4=>[16],
                    5=>[2, 5 ,4, 1, 3, 6,],
                    6=>['#003c00'],
                    7=>[0]
                ]
            ], $user1, $user2);
        $this->assertEquals([ 0 => 0.0,
                1 => 0.0,
                2 => 0.0,
                3 => 0.0,
                4 => 0.0,
                5 => 0.0,
                6 => 0.0,
                7 => 0.0], $result);
    }


    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     */
    public function testUserDistances8()
    {
        $user1 = new User();
        $user1->gender=3; //user1 is a other
        $user1->id=71;
        $user2 = new User();
        $user2->gender=4; //user2 is a unspecified
        $user2->id=72;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [71 => null]], 2 => [[$user2], [72 => null]]]);
        $result = $algorithm->userDistances(
            [
                71 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[1]
                ],
                72 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[5, 1,2,3,4,6],
                    3=>[65.351167,-59.3491597],
                    4=>[23],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c01'],
                    7=>[1]
                ]
            ], $user1, $user2);
        $this->assertEquals([ 0 => 0.0,
                1 => 0.0,
                2 => 0.5345224838248487930769670128938741981983184814453125,
                3 => 0.553407341450889322942430226248688995838165283203125,
                4 => 0.05833333333333333425851918718763045035302639007568359375,
                5 => 0.0,
                6 => 0.00226411870270443356389922229254807461984455585479736328125,
                7 => 1000.0], $result);
    }


    /**
     * Test user distances method which returns an array of distances
     * Returns the euclidean distance between 2 users
     * based on their questionnaire results
     * Small distances in array for qs returned due to similarities in answers
     * except large difference due to age preference
     */
    public function testUserDistances9()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=431;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=472;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=> 1.0],
            [1 => [[$user1], [431 => null]], 2 => [[$user2], [472 => null]]]);
        $result = $algorithm->userDistances(
            [
                431 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[1]
                ],
                472 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[5, 1,2,3,4,6],
                    3=>[65.351167,-59.3491597],
                    4=>[23],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c01'],
                    7=>[1]
                ]
            ], $user1, $user2);
        $this->assertEquals([ 0 => 0.0,
            1 => 0.0,
            2 => 0.5345224838248487930769670128938741981983184814453125,
            3 => 0.553407341450889322942430226248688995838165283203125,
            4 => 0.05833333333333333425851918718763045035302639007568359375,
            5 => 0.0,
            6 =>0.00226411870270443356389922229254807461984455585479736328125,
            7 => 1000.0], $result);
    }



    /**
     *Test the pair and user distance
     *returns distance between the pair's answers and the user's answer
     */
    public function testPairAndUserDistance1()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=256;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=257;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [256 => null]], 2 => [[$user2], [257 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                256 => [
                    0=>[1],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[61.7420487,8.4405546],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#fafa00'],
                    7=>[1]
                ],
                257 => [
                    0=>[1],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[6, 5 ,4, 3,1,2],
                    6=>['#fa0000'],
                    7=>[1]
                ]
            ], [$user1, $user2], $user1);
        $this->assertEquals(62562.53125, $result, '', 0.00001);
    }

    /**
     *
     */
    public function testPairAndUserDistance2()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=256;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=257;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [256 => null]], 2 => [[$user2], [257 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                256 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[61.7420487,8.4405546],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#fafa00'],
                    7=>[0]
                ],
                257 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[6, 5 ,4, 3,1,2],
                    6=>['#fa0000'],
                    7=>[0]
                ]
            ], [$user1, $user2], $user2);
        $this->assertEquals(0.050512089, $result, '', 0.000000001);
    }


    /**
     *
     */
    public function testPairAndUserDistance3()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                1 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[0]
                ],
                2 => [
                    0=>[1],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.90049893],
                    4=>[26],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#00fb00'],
                    7=>[0]
                ]
            ], [$user1, $user2], $user1);
        $this->assertEquals(62500.000000006, $result,'',0.000000001);
    }

    /**
     *
     */
    public function testPairAndUserDistance4()
    {
        $user1 = new User();
        $user1->gender=2; //user1 is a female
        $user1->id=19;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=20;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [19 => null]], 2 => [[$user2], [20 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                19 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,6,4,2,5,3],
                    3=>[20.0121253, 64.446581],
                    4=>[18],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#003c00'],
                    7=>[0]
                ],
                20 => [
                    0=>[0],
                    1=>[ 2, 1, -1, 1, 0, -2],
                    2=>[1,6,4,2,5,3],
                    3=>[11.47837, 156.8949893],
                    4=>[17],
                    5=>[1, 2, 3, 4, 5, 6],
                    6=>['#006100'],
                    7=>[0]
                ]
            ], [$user1, $user2], $user2);
        $this->assertEquals(0.045206361, $result,'',0.000000001);
    }

    /**
     *
     */
    public function testPairAndUserDistance5()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=55;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=56;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [55 => null]], 2 => [[$user2], [56 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                55 => [
                    0=>[0],
                    1=>[-2, 1, 1, 0, -1, 2],
                    2=>[4,1,2,3, 5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[18],
                    5=>[1, 4, 5, 6, 2, 3],
                    6=>['#003c00'],
                    7=>[0]
                ],
                56 => [
                    0=>[0],
                    1=>[ 2, -2, 1, -1, 1, 0,],
                    2=>[1,5,2,3,4,6],
                    3=>[11.47837, 156.8949893],
                    4=>[19],
                    5=>[1, 4, 5, 6, 3, 2],
                    6=>['#00fb00'],
                    7=>[0]
                ]
            ], [$user1, $user2], $user2);
        $this->assertEquals(0.062001284, $result,'',0.000000001);
    }

    public function testPairAndUserDistance6()
    {
        $user1 = new User();
        $user1->gender=1; //user1 is a male
        $user1->id=1;
        $user2 = new User();
        $user2->gender=2; //user2 is a female
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                1 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[1]
                ],
                2 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.47837, 156.8949893],
                    4=>[26],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#00fb00'],
                    7=>[1]
                ]
            ], [$user1, $user2], $user2);
        $this->assertEquals(125.00000607972, $result, '', 0.00000001);
    }

    /**
     * Test the pair and user distance
     * returns 0 for the distance between the pair's answers and the
     * user's answer because answers are identical
     */
    public function testPairAndUserDistance7()
    {
        $user1 = new User();
        $user1->gender=4; //user1 is a unspecified
        $user1->id=1;
        $user2 = new User();
        $user2->gender=4; //user2 is a unspecified
        $user2->id=2;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [1 => null]], 2 => [[$user2], [2 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                1 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[20.0121253, 64.446581],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[0]
                ],
                2 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[20.0121253, 64.446581],
                    4=>[16],
                    5=>[2, 5 ,4, 1, 3, 6,],
                    6=>['#003c00'],
                    7=>[0]
                ]
            ], [$user1, $user2], $user1);
        $this->assertEquals(0, $result);
    }



    /**
     *
     */
    public function testPairAndUserDistance8()
    {
        $user1 = new User();
        $user1->gender=3; //user1 is a other
        $user1->id=71;
        $user2 = new User();
        $user2->gender=4; //user2 is a unspecified
        $user2->id=72;
        $scheme = new Scheme();
        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences
        $algorithm = Algorithm:: newAlgorithm($scheme,[
            0=>['type_id'=>-1],
            1=>['type_id'=>1, 'validation' => ['min'=>-2, 'max'=>2, 'options'=>5]],
            2 => ['type_id' => 2, 'validation' => ['options' => 6]],
            3=>['type_id'=>3],
            4=>['type_id'=>7, 'validation' => ['min'=>0, 'max'=>120]],
            5=>['type_id'=>6, 'validation' => ['options'=>10]],
            6=>['type_id'=>8],
            7=>['type_id'=>-2] ],
            [0=>1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4=>1.0, 5=>1.0, 6=>1.0, 7=>1.0],
            [1 => [[$user1], [71 => null]], 2 => [[$user2], [72 => null]]]);
        $result = $algorithm->pairAndUserDistance(
            [
                71 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[1,2,3,4,5,6],
                    3=>[11.4898009,152.4098799],
                    4=>[16],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c00'],
                    7=>[1]
                ],
                72 => [
                    0=>[0],
                    1=>[-2, 1, -1, 2, 1, 0],
                    2=>[5, 1,2,3,4,6],
                    3=>[65.351167,-59.3491597],
                    4=>[23],
                    5=>[2, 1, 3, 6, 5 ,4],
                    6=>['#003c01'],
                    7=>[1]
                ]
            ], [$user1, $user2], $user1);
        $this->assertEquals(125.00001860568, $result, '',0.000000001);
    }


    /**
     *
     */
    public function testPairAndUserDistance9()
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
        $result = $algorithm->pairAndUserDistance(
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
            ], [$user1, $user2], $user2);
        $this->assertEquals(0.0626128119768, $result, '', 0.000000001);
    }

    /**
     * Test individual mapping method which assigns 1 buddy to 2 newbies.
     * Mocked getBuddiesMaxNewbies to create an mapping with 1 buddy to 2 newbies
     * Returns an array containing 2 arrays matched by index, the first array of buddies
     * to the second array of 2 newbies assigned to 1 buddy
     * Tested with 3 buddies (1 male, 2 female) and 6 newbies (3 males, 3 females)
     */
    public function testCreateIndividualMappingWith1BuddyTo2Newbies()
    {
        $buddy1 = new User();
        $buddy1->gender = 1; //buddy1 is a male
        $buddy1->id = 1;
        $buddy2 = new User();
        $buddy2->gender = 2; //buddy2 is a female
        $buddy2->id = 2;
        $buddy3 = new User();
        $buddy3->gender = 2; //buddy3 is a female
        $buddy3->id = 3;
        $newbie1 = new User();
        $newbie1->gender = 1; //newbie1 is a male
        $newbie1->id = 4;
        $newbie2 = new User();
        $newbie2->gender = 1; //newbie2 is a male
        $newbie2->id = 5;
        $newbie3 = new User();
        $newbie3->gender = 2; //newbie3 is a female
        $newbie3->id = 6;
        $newbie4 = new User();
        $newbie4->gender = 2; //newbie4 is a female
        $newbie4->id = 7;
        $newbie5 = new User();
        $newbie5->gender = 2; //newbie5 is a female
        $newbie5->id = 8;
        $newbie6 = new User();
        $newbie6->gender = 1; //newbie6 is a male
        $newbie6->id = 9;
        $mockedScheme = Mockery::mock(Scheme::class);

        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences

        $algorithm = $this->getMockBuilder(Algorithm::class)->setMethods(['getBuddiesMaxNewbies'])->getMock();
        $algorithm->initialiseVariables(
            [
                0 => ['type_id' => -1],
                1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
                2 => ['type_id' => 2, 'validation' => ['options' => 6]],
                3 => ['type_id' => 3],
                4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
                5 => ['type_id' => 6, 'validation' => ['options' => 10]],
                6 => ['type_id' => 8],
                7 => ['type_id' => -2]
            ],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [
                1 => [[$buddy1, $buddy2, $buddy3], [1 => null, 2 => null, 3 => null]],
                2 => [[$newbie1, $newbie2, $newbie3, $newbie4, $newbie5, $newbie6], [4 => null, 5 => null, 6 => null, 7 => null, 8 => null, 9 => null]],
            ],
            $mockedScheme
        );
        $algorithm->expects($this->any())->method('getBuddiesMaxNewbies')->willReturn([1 => 2, 2 => 2, 3 => 2]);
        $result = $algorithm->createIndividualMapping(
            [$buddy1, $buddy2, $buddy3],
            [$newbie1, $newbie2, $newbie3, $newbie4, $newbie5, $newbie6],
            [
                1 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c05'],
                    7 => [0]
                ],
                2 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 5, 4, 1, 3, 6,],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                3 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [30.0497079, 60.3336572],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c10'],
                    7 => [0]
                ],
                4 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [6, 5, 4, 3, 2, 1],
                    3 => [20.0121253, 64.446581],
                    4 => [17],
                    5 => [5, 4, 2, 1, 3, 6],
                    6 => ['#003c01'],
                    7 => [0]
                ],
                5 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [20.0121253, 64.446581],
                    4 => [18],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#003c04'],
                    7 => [0]
                ],
                6 => [
                    0 => [1],
                    1 => [2, 1, -1, 1, 0, -2],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [11.47837, 156.8949893],
                    4 => [17],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#006100'],
                    7 => [0]
                ],
                7 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 5, 4, 1, 3, 6,],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                8 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [30.0497079, 60.3336572],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                9 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [6, 5, 4, 3, 2, 1],
                    3 => [20.0121253, 64.446581],
                    4 => [17],
                    5 => [5, 4, 2, 1, 3, 6],
                    6 => ['#003c01'],
                    7 => [0]
                ]
            ]
        );
        $this->assertEquals([
            [
                [$buddy1],
                [$buddy2],
                [$buddy3]
            ],
            [
               [$newbie2, $newbie1],
               [$newbie4, $newbie3],
               [$newbie5, $newbie6]
            ]
        ], $result);
    }

    /**
     * Test individual mapping method which assigns 1 buddy to 2 newbies.
     * Mocked getBuddiesMaxNewbies to create an mapping with 1 buddy to 2 newbies
     * Returns an array containing 2 arrays matched by index, the first array of buddies
     * to the second array of 1 newbie assigned to 1 buddy
     * Tested with 3 buddies (1 male, 2 female) and 6 newbies (3 males, 3 females)
     */
    public function testCreateIndividualMappingWith1BuddyTo1Newbie()
    {
        $buddy1 = new User();
        $buddy1->gender = 1; //buddy1 is a male
        $buddy1->id = 1;
        $buddy2 = new User();
        $buddy2->gender = 2; //buddy2 is a female
        $buddy2->id = 2;
        $buddy3 = new User();
        $buddy3->gender = 2; //buddy3 is a female
        $buddy3->id = 3;
        $newbie1 = new User();
        $newbie1->gender = 1; //newbie1 is a male
        $newbie1->id = 4;
        $newbie2 = new User();
        $newbie2->gender = 1; //newbie2 is a male
        $newbie2->id = 5;
        $newbie3 = new User();
        $newbie3->gender = 2; //newbie3 is a female
        $newbie3->id = 6;
        $newbie4 = new User();
        $newbie4->gender = 2; //newbie4 is a female
        $newbie4->id = 7;
        $newbie5 = new User();
        $newbie5->gender = 2; //newbie5 is a female
        $newbie5->id = 8;
        $newbie6 = new User();
        $newbie6->gender = 1; //newbie6 is a male
        $newbie6->id = 9;
        $mockedScheme = Mockery::mock(Scheme::class);

        //new algorithm parameters => scheme, questionsMapping, questionsWeighting, unpairedUsersPreferences

        $algorithm = $this->getMockBuilder(Algorithm::class)->setMethods(['getBuddiesMaxNewbies'])->getMock();
        $algorithm->initialiseVariables(
            [
                0 => ['type_id' => -1],
                1 => ['type_id' => 1, 'validation' => ['min' => -2, 'max' => 2, 'options' => 5]],
                2 => ['type_id' => 2, 'validation' => ['options' => 6]],
                3 => ['type_id' => 3],
                4 => ['type_id' => 7, 'validation' => ['min' => 0, 'max' => 120]],
                5 => ['type_id' => 6, 'validation' => ['options' => 10]],
                6 => ['type_id' => 8],
                7 => ['type_id' => -2]
            ],
            [0 => 1.0, 1 => 1.0, 2 => 1.0, 3 => 1.0, 4 => 1.0, 5 => 1.0, 6 => 1.0, 7 => 1.0],
            [
                1 => [[$buddy1, $buddy2, $buddy3], [1 => null, 2 => null, 3 => null]],
                2 => [[$newbie1, $newbie2, $newbie3, $newbie4, $newbie5, $newbie6], [4 => null, 5 => null, 6 => null, 7 => null, 8 => null, 9 => null]],
            ],
            $mockedScheme
        );
        $algorithm->expects($this->any())->method('getBuddiesMaxNewbies')->willReturn([1 => 1, 2 => 1, 3 => 1]);
        $result = $algorithm->createIndividualMapping(
            [$buddy1, $buddy2, $buddy3],
            [$newbie1, $newbie2, $newbie3, $newbie4, $newbie5, $newbie6],
            [
                1 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c05'],
                    7 => [0]
                ],
                2 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 5, 4, 1, 3, 6,],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                3 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [30.0497079, 60.3336572],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c10'],
                    7 => [0]
                ],
                4 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [6, 5, 4, 3, 2, 1],
                    3 => [20.0121253, 64.446581],
                    4 => [17],
                    5 => [5, 4, 2, 1, 3, 6],
                    6 => ['#003c01'],
                    7 => [0]
                ],
                5 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [20.0121253, 64.446581],
                    4 => [18],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#003c04'],
                    7 => [0]
                ],
                6 => [
                    0 => [1],
                    1 => [2, 1, -1, 1, 0, -2],
                    2 => [1, 6, 4, 2, 5, 3],
                    3 => [11.47837, 156.8949893],
                    4 => [17],
                    5 => [1, 2, 3, 4, 5, 6],
                    6 => ['#006100'],
                    7 => [0]
                ],
                7 => [
                    0 => [1],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [20.0121253, 64.446581],
                    4 => [16],
                    5 => [2, 5, 4, 1, 3, 6,],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                8 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [1, 2, 3, 4, 5, 6],
                    3 => [30.0497079, 60.3336572],
                    4 => [16],
                    5 => [2, 1, 3, 6, 5, 4],
                    6 => ['#003c00'],
                    7 => [0]
                ],
                9 => [
                    0 => [0],
                    1 => [-2, 1, -1, 2, 1, 0],
                    2 => [6, 5, 4, 3, 2, 1],
                    3 => [20.0121253, 64.446581],
                    4 => [17],
                    5 => [5, 4, 2, 1, 3, 6],
                    6 => ['#003c01'],
                    7 => [0]
                ]
            ]
        );
        $this->assertEquals([
            [
                [$buddy1],
                [$buddy2],
                [$buddy3]
            ],
            [
                [$newbie2],
                [$newbie4],
                [$newbie5]
            ]
        ], $result);
    }




}

