<!DOCTYPE html >
<html>
    <head>
        <title></title>
    </head>
    <body>
        <h1>Hello, World</h1>
        <ul>
            @foreach ($shoppinglists as $shoppinglist)
                <li><a href="shoppinglists/{{$shoppinglist->id}}">
                        {{$shoppinglist->creation_date}}{{$shoppinglist->shopping_date}}</a></li>
            @endforeach
        </ul>
    </body>
</html>