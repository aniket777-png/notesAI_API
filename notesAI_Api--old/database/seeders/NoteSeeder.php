<?php
namespace Database\Seeders; use Illuminate\Database\Seeder; use App\Models\Note;
class NoteSeeder extends Seeder{public function run():void{for($i=1;$i<=20;$i++){Note::create(['title'=>"Sample Note $i",'content'=>"This is dummy content for note $i about Laravel APIs and AI."]);}}}
