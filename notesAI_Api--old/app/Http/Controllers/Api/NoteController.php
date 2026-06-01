<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;use Illuminate\Http\Request;use App\Models\Note;
class NoteController extends Controller{
public function index(Request $r){return response()->json(Note::paginate($r->limit??10));}
public function store(Request $r){$d=$r->validate(['title'=>'required|max:255','content'=>'required']);return response()->json(Note::create($d),201);} 
public function show($id){$n=Note::find($id);return $n?response()->json($n):response()->json(['message'=>'Not Found'],404);} 
public function update(Request $r,$id){$n=Note::findOrFail($id);$n->update($r->validate(['title'=>'required','content'=>'required']));return response()->json($n);} 
public function destroy($id){Note::findOrFail($id)->delete();return response()->json(['message'=>'Deleted']);}
public function summary($id){$n=Note::findOrFail($id); return response()->json(['summary'=>substr($n->content,0,120)]);} 
public function search(Request $r){$q=$r->q; return response()->json(Note::where('title','like',"%$q%")->orWhere('content','like',"%$q%")->get());}
}
