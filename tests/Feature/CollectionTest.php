<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }
    
    public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        foreach($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1,2,3);
        assertEqualsCanonicalizing([1,2,3], $collection->all());

        $result = $collection->pop();
        assertEquals(3, $result);
        assertEqualsCanonicalizing([1,2], $collection->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Eko"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Eko")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([["Eko", "Kurniawan"], ["Arnold", "Supriyadi"]]);
        $result = $collection->mapSpread(function ($firstName, $lastName){
            $fullName = $firstName . " ". $lastName;
            return new Person($fullName);
        });

        assertEquals([
            new Person("Eko Kurniawan"),
            new Person ("Arnold Supriyadi")
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "Eko",
                "department" => "IT"
            ],
            [
                "name" => "Arnold",
                "department" => "IT"
            ],
            [
                "name" => "Imam",
                "department" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["department"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "IT" => collect(["Eko", "Arnold"]),
            "HR" => collect(["Imam"])
        ], $result->all());
    }
}
