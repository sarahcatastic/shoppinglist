<?php

use Illuminate\Database\Seeder;

class ShoppinglistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        DB::table('shoppinglists')->insert([
            'creation_date' => new DateTime(),
            'shopping_date' => date("2020-05-25"),
            'shopping_price' => 25.50
        ]);
        */

        $helga = \App\User::where('id', '=', '1')->first();

        $shoppinglist1 = new \App\ShoppingList();
        $shoppinglist1->creator()->associate($helga);
        $shoppinglist1->shopping_date = date("2020-05-25");
        $shoppinglist1->save();

        // Item hinzufügen
        $item1 = new \App\Item;
        $item1->article = 'Nudeln';
        $item1->amount = '2';
        $item1->maxPrice = '2';
        $item1->shopping_list()->associate($shoppinglist1);
        $item1->save();

        $shoppinglist1_comment1 = new \App\Comment;
        $shoppinglist1_comment1->text = "Vielen Dank für Ihre Hilfe! Ich kann leider derzeit aufgrund meines Hustens nicht einkaufen gehen.";
        $shoppinglist1_comment1->user()->associate($helga);
        $shoppinglist1_comment1->shopping_list()->associate($shoppinglist1);
        $shoppinglist1_comment1->save();
    }
}
