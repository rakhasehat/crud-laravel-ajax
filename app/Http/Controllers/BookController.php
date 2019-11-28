<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            // Mendapatkan data dari database menggunakan model
            $data = Book::latest()->get();

            return DataTables::of($data)
                        ->addColumn('action', function($data){
                            // edit button
                            $button = '<button type="button"
                            name="edit" id="'.$data->id.'"
                            class="edit btn btn-warning btn-sm">
                            Edit</button>';

                            // delete button
                            $button .= '
                            &nbsp;&nbsp;<button type="button"
                            name="delete" id="'.$data->id.'"
                            class="delete btn btn-danger btn-sm">
                            Delete</button>';

                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
        }
        return view('home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $error = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ],
        [
            'title.required' => 'Field judul harus diisi!',
            'description.required' => 'Field deskripsi harus diisi!'
        ]);

        if($error->fails())
        {
            return response()->json([ 'errors' => $error->errors()->all() ]);
        }

        $data = array(
            'title' => $request->title,
            'description' => $request->description
        );

        Book::create($data);

        return response()->json([ 'success' => 'Data Added Successfully.' ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = Book::findOrFail($id);
            return response()->json([ 'result' => $data ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        // Validasi form
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ],
        [
            'title.required' => 'Field judul harus diisi!',
            'description.required' => 'Field deskripsi harus diisi!'
        ]);

        // Cek kondisi validator
        if($validate->fails())
        {
            return response()->json([ 'errors' => $validate->errors()->all() ]);
        }

        $data = array(
            'title' => $request->title,
            'description' => $request->description
        );

        // Proses update
        Book::whereId($request->hidden_id)->update($data);

        return response()->json([ 'success' => 'Data successfully updated' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Book::findOrFail($id);
        $data->delete();
    }
}
