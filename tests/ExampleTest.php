<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Book;
use App\User;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_books()
    {
        Book::create([
            'name' => 'Solaris',
            'author' => 'Lem',
            'genre' => 'sciense fiction',
            'user_id' => 1,
            'image' => 'img'
        ]);
        $this->seeInDatabase('books',['name' => 'Solaris']);
    }
}
