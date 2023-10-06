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

    public function testZip()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->zip($collection2);

        assertEquals([
            collect([1,4]),
            collect([2,5]),
            collect([3,6]),
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1,2,3]);
        $collection2 = collect([4,5,6]);
        $collection3 = $collection1->concat($collection2);

        assertEquals([1,2,3,4,5,6], $collection3->all());

    }

    public function testCombine()
    {
        $collection1 = ["name", "country"];
        $collection2 = ["Eko", "Indonesia"];
        $collection3 = collect($collection1)->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name" => "Eko",
            "country" => "Indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);

        $result = $collection->collapse();
        assertEquals([1,2,3,4,5,6,7,8,9], $result->all());
    }

    public function testMap()
    {
        $collection = collect([
            [
                "name" => "Eko",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Khannedy",
                "hobbies" => ["Reading", "Writing"]
            ]
        ]);

        $hobbies = $collection->flatMap(function ($item){
            return $item["hobbies"];
        });

        assertEquals(["Coding","Gaming","Reading","Writing"], $hobbies->all());
    }

    public function testJoin()
    {
        $collection = collect(["Eko", "Khannedy", "Arnold"]);

        assertEquals("Eko-Khannedy-Arnold", $collection->join("-"));
        assertEquals("Eko-Khannedy_Arnold", $collection->join("-", "_"));
    }

    public function testFilter()
    {
        $collection = collect([
            "Eko" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        $result = $collection->filter(function ($value, $key){
            return $value >=90;
        });

        assertEquals([
            "Eko" => 100,
            "Joko" => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key){
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2,4,6,8,10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "Eko" => 100,
            "Budi" => 80,
            "Joko" => 90
        ]);
        [$result1, $result2] = $collection->partition(function ($value, $key){
            return $value >= 90;
        });

        assertEquals(["Eko" => 100, "Joko" => 90], $result1->all());
        assertEquals(["Budi" => "80"], $result2->all());
    }
}
