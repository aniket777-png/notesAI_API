<?php
namespace Database\Seeders; use Illuminate\Database\Seeder; use App\Models\Note; class NoteSeeder extends Seeder{ public function run():void{ for($i=1;$i<=20;$i++){ Note::create(['title'=>"Demo Note $i",'content'=>"Demo content $i"]); } } }
