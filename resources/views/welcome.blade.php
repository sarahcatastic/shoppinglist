<!DOCTYPE html >
<html>
    <head>
        <title></title>
    </head>
    <body>
        <h1>Hello, World</h1>
        <ul>
            @foreach ($shoppinglists as $shoppinglist)
                <li>{{$shoppinglist->creation_date}} {{$shoppinglist->shopping_date}}</li>
            @endforeach
        </ul>
    </body>
</html >