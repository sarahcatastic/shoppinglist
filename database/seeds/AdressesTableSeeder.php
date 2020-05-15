<?php

use Illuminate\Database\Seeder;

class AdressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adress1 = new \App\Adress;
        $adress1->PLZ = '1210';
        $adress1->city = 'Wien';
        $adress1->street = 'Eibnergasse 2';
        $adress1->country = 'AUT';
        $adress1->save();

        $adress2 = new \App\Adress;
        $adress2->PLZ = '1220';
        $adress2->city = 'Wien';
        $adress2->street = 'Bergengasse 5';
        $adress2->country = 'AUT';
        $adress2->save();

        $adress3 = new \App\Adress;
        $adress3->PLZ = '1210';
        $adress3->city = 'Wien';
        $adress3->street = 'Eibnergasse 13';
        $adress3->country = 'AUT';
        $adress3->save();

        $adress4= new \App\Adress;
        $adress4->PLZ = '1220';
        $adress4->city = 'Wien';
        $adress4->street = 'Bergengasse 8';
        $adress4->country = 'AUT';
        $adress4->save();
    }
}
