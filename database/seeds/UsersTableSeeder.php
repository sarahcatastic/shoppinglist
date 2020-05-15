<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Creator 1
        $adress1 = \App\Adress::where('id', '=', '1')->first();

        $creator1 = new \App\User;
        $creator1->firstname = "Helga";
        $creator1->lastname = "Brugger";
        $creator1->email = "helga@gmail.com";
        $creator1->password = bcrypt("helga");
        $creator1->is_helping = false;
        $creator1->adress()->associate($adress1);
        $creator1->save();


        // Creator 2
        $adress2 = \App\Adress::where('id', '=', '2')->first();

        $creator2 = new \App\User;
        $creator2->firstname = "Friedl";
        $creator2->lastname = "Diringer";
        $creator2->email = "friedl@gmail.com";
        $creator2->password = bcrypt("friedl");
        $creator2->is_helping = false;
        $creator2->adress()->associate($adress2);
        $creator2->save();

        // Creator 3

        // same address as creator 1
        $creator3 = new \App\User;
        $creator3->firstname = "Wolfgang";
        $creator3->lastname = "Brugger";
        $creator3->email = "wolfgang@gmail.com";
        $creator3->password = bcrypt("wolfgang");
        $creator3->is_helping = false;
        $creator3->adress()->associate($adress1);
        $creator3->save();

        //Volunteer 1

        $adress3 = \App\Adress::where('id', '=', '3')->first();

        $volutneer1 = new \App\User;
        $volutneer1->firstname = "Max";
        $volutneer1->lastname = "Steiner";
        $volutneer1->email = "max@gmail.com";
        $volutneer1->password = bcrypt("max");
        $volutneer1->is_helping = true;
        $volutneer1->adress()->associate($adress3);
        $volutneer1->save();

        //Volunteer 2

        $adress4 = \App\Adress::where('id', '=', '4')->first();

        $volutneer2 = new \App\User;
        $volutneer2->firstname = "Susi";
        $volutneer2->lastname = "Simmer";
        $volutneer2->email = "susi@gmail.com";
        $volutneer2->password = bcrypt("susi");
        $volutneer2->is_helping = true;
        $volutneer2->adress()->associate($adress4);
        $volutneer2->save();


    }
}
