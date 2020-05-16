<?php

namespace App\Http\Controllers;

use App\ShoppingList;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\Comment;
use App\User;

use App\Http\Controllers\Auth\ApiAuthController;
use JWTAuth;


class ShoppinglistController extends Controller
{
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        // Inhalte aus DB Books rausholen mit Befehl get();
        //$shoppinglists = DB::table('shoppinglists')->get();
        // /*->where('volunteer_id', '=', $user['id'])*/
        $shoppinglists = ShoppingList::where(function ($query) {
            $query->where('status', '=', 'Beantragt')->where('volunteer_id', '=', null);})
            ->orWhere(function ($query) use ($user) {
                $query->where('status', '=', 'Gebucht')->where('volunteer_id', '=', $user['id']);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('status', '=', 'Abgeschlossen')->where('volunteer_id', '=', $user['id']);
            })
            /*
            ->orWhere(['status', 'Bestellt'], ['volunteer_id', $user['id']])
            */
            ->with('volunteer', 'comments', 'items')->get();
        return $shoppinglists;
    }


    public function show(ShoppingList $shoppingList)
    {
        // Inhalte aus DB Books rausholen mit Befehl find und nach dieser ID suchen
        //$shopping_list = DB::table('shoppinglists')->find($id);
        return view('shoppinglists.show', compact('shoppinglist'));
    }

    public function findShoppinglistById($id):ShoppingList
    {
        // where gibt mehrere zurück, first beschneidet es auf das erste
        // auch wenn id eigentlich unique ist
        $shoppinglist = shoppinglist::where('id', $id)->with('creator', 'volunteer', 'comments', 'items')->first();
        return $shoppinglist;
    }

    public function getUsernameById($id):User
    {
        // where gibt mehrere zurück, first beschneidet es auf das erste
        // auch wenn id eigentlich unique ist
        $user = user::where('id', $id)->with('adress')->first();
        return $user;
    }

    public function findShoppingListsByCreator() {
        $user = JWTAuth::parseToken()->authenticate();
        $shoppinglists = ShoppingList::with(['creator', 'comments', 'items'])
            ->where('creator_id', $user['id'])->get();
        return $shoppinglists;
    }

    public function findShoppingListsByVolunteer() {
        $user = JWTAuth::parseToken()->authenticate();
        $shoppinglists = ShoppingList::with(['volunteer', 'comments', 'items'])
            ->where('volunteer_id', $user['id'])->get();
        return $shoppinglists;
    }

    // neue Shoppinglist erstellen aus Request mit post
    public function save(Request $request) : JsonResponse {
        $request = $this->parseRequest($request);
        $user = JWTAuth::parseToken()->authenticate();

        //
        DB::beginTransaction();
        try {
            // wenn man sich an Konventionen hält und in dem JSON diese auch stimmen
            // werden Properties automatisch gemappt und gesetzt
            $shoppinglist = ShoppingList::create(['id' => $request['id'], 'shopping_date' => $request['shopping_date'], 'creator_id' => $user['id']]);

            // Items speichern
            if($request['items'] && is_array($request['items'])) {
                foreach($request['items'] as $item) {
                    $item = Item::create(['article'=>$item['article'], 'description'=>$item['description'], 'amount'=>$item['amount'], 'maxPrice'=>$item['maxPrice'], 'shopping_list_id'=> $shoppinglist->id]);
                    $shoppinglist->items()->save($item);
                }
            }

            if($request['comments'] && is_array($request['comments'])) {
                foreach($request['comments'] as $comment) {
                    $comment = Comment::create(['text' => $comment['text'], 'shopping_list_id'=> $shoppinglist->id, 'user_id'=> $user['id']]);
                    $shoppinglist->comments()->save($comment);
                }
            }

            // Transaktion abschließen
            DB::commit();

            return response()->json($shoppinglist,201);

        } catch (\Exception $e) {
            // wenn Transaktion fehlgeschlagen hat, kommt eine Fehlermeldung
            DB::rollback();
            return response()->json("saving shoppinglist failed: " .$e->getMessage(),420);
        }
    }

    // Methode um Datum für DB verständlich zu machen
    private function parseRequest(Request $request) : Request {
        // Datum parsen nach ISO 8601
        // "2020-01-01T21:00:00.000Z"
        // so brauchts die DB
        $date = new \DateTime($request->shopping_date);
        $request['shopping_date'] = $date;
        return $request;
    }

    public function edit(Request $request, $id) : JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                    $request = $this->parseRequest($request);
                    $shoppinglist->update(['shopping_date' => $request['shopping_date']]);

                    //delete all old items
                    $shoppinglist->items()->delete();
                    // save items
                    if (isset($request['items']) && is_array($request['items'])) {
                        foreach ($request['items'] as $item) {
                            $item = Item::firstOrNew(['article'=>$item['article'], 'description'=>$item['description'], 'amount'=>$item['amount'], 'maxPrice'=>$item['maxPrice']]);
                            $shoppinglist->items()->save($item);
                        }
                    }
            }
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }

    public function addComment(Request $request, $id) : JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                $request = $this->parseRequest($request);

                $shoppinglist->comments()->delete();
                if($request['comments'] && is_array($request['comments'])) {
                    foreach($request['comments'] as $comment) {
                        $comment = Comment::create(['text' => $comment['text'], 'shopping_list_id'=> $shoppinglist->id, 'user_id'=> $user['id']]);
                        $shoppinglist->comments()->save($comment);
                    }
                }

            }
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }

    public function delete(string $id) : JsonResponse
    {
        // nach Id suchen
        $shoppinglist = ShoppingList::where('id', $id)->first();
        // wenn es eine Shoppinglist gibt
        if ($shoppinglist != null){
            $shoppinglist->delete();
        }
        else
            throw new \Exception("shoppinglist couldn't be deleted - it does not exist");
        return response()->json('Shoppinglist (' . $id . ') successfully deleted', 200);

    }

    public function sendShoppinglist(Request $request, $id) : JsonResponse
    {
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                $request = $this->parseRequest($request);
                $shoppinglist->update(['status' => 'Beantragt']);

            }
            $shoppinglist->save();
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['creator', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }

    public function updateVolunteer(Request $request, $id) : JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                $request = $this->parseRequest($request);
                $shoppinglist->update(['shopping_price' => $request['shopping_price'], 'status' => $request['status']]);

                $shoppinglist->comments()->delete();
                if($request['comments'] && is_array($request['comments'])) {
                    foreach($request['comments'] as $comment) {
                        $comment = Comment::create(['text' => $comment['text'], 'shopping_list_id'=> $shoppinglist->id, 'user_id'=> $user['id']]);
                        $shoppinglist->comments()->save($comment);
                    }
                }
            }


            $shoppinglist->save();
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }

    public function bookShoppinglist(Request $request, $id) : JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                $request = $this->parseRequest($request);
                $shoppinglist->update(['volunteer_id' => $user['id'], 'status' => 'Gebucht']);

            }
            $shoppinglist->save();
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }

    public function closeShoppinglist(Request $request, $id) : JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        DB::beginTransaction();
        try {
            $shoppinglist = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();

            if ($shoppinglist != null) {
                $request = $this->parseRequest($request);
                $shoppinglist->update(['volunteer_id' => $user['id'], 'status' => 'Abgeschlossen']);

            }
            $shoppinglist->save();
            DB::commit();
            $shoppinglist1 = ShoppingList::with(['volunteer', 'comments', 'items'])
                ->where('id', $id)->first();
            // return a vaild http response
            return response()->json($shoppinglist1, 201);
        }
        catch (\Exception $e) {
            // rollback all queries
            DB::rollBack();
            return response()->json("updating shoppinglist failed: " . $e->getMessage(), 420);
        }
    }



}
