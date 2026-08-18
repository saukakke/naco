<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Post;
use App\Models\Rank;
use App\Models\RankCategory;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class NacoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 6) as $i) Unit::firstOrCreate(['code' => chr(64 + $i)], ['name' => 'Unit '.chr(64 + $i)]);

        $categories = ['Other Ranks','Junior Officers','Senior Officers','Superior Officers'];
        $categoryIds = [];
        foreach ($categories as $i => $name) {
            $categoryIds[$name] = RankCategory::updateOrCreate(['slug' => str($name)->slug()], ['name' => $name, 'order' => $i + 1])->id;
        }

        $ranks = [
            ['Private','Other Ranks'], ['Copral','Other Ranks'], ['Sergeant','Other Ranks'], ['Staff Sergeant','Other Ranks'], ['Senior Staff Sergeant','Other Ranks'], ['Warrant Officer 2','Other Ranks'], ['Warrant Officer 1','Other Ranks'],
            ['Second Lieutenant','Junior Officers'], ['Lieutenant','Junior Officers'], ['Captain','Junior Officers'],
            ['Master','Senior Officers'], ['Senior Master','Senior Officers'], ['Right Comrade','Senior Officers'],
            ['Engineer','Superior Officers'], ['Chief Engineer','Superior Officers'], ['Rear Marshal','Superior Officers'], ['Cadet Marshal','Superior Officers'],
        ];
        foreach ($ranks as $i => [$name, $category]) Rank::updateOrCreate(['slug' => str($name)->slug()], ['name' => $name, 'rank_category_id' => $categoryIds[$category], 'order' => $i + 1]);

        foreach ([['DRILL','Drill'],['BF','BF'],['DS','DS'],['ISLAMIC','Islamic'],['ADMIN','Admin']] as [$code,$name]) Course::firstOrCreate(['code'=>$code], ['name'=>$name,'status'=>'active']);

        $posts = [
            ['General Officer','national'], ['Chief Instructor','national'],
            ['State Controller','state'], ['Deputy State Controller','state'], ['National Medical Director','state'], ['Auditor','state'], ['Secretary','state'], ['National Parade Commander','state'], ['National Intelligent Director','state'], ['National Provost Marshal','state'], ['Unit Sergeant Major','state'],
            ['Chairman Self-Reliance','lga'], ['HCS','ward'],
        ];
        foreach ($posts as [$name,$level]) Post::firstOrCreate(['slug'=>str($name)->slug()], ['name'=>$name,'level'=>$level]);
    }
}
