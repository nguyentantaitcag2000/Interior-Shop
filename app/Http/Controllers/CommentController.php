<?php

namespace App\Http\Controllers;

use App\Models\comment;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(int $idPorduct)
    {
        $data = comment::with(['replies','replies.user','parentComment','user'])->where('ID_Product', $idPorduct)->where('reply_to',NULL)->get();
        
        return json_encode([
            'status' => 200,
            'message' => 'success',
            'object' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authRepository->CheckLogin();
        comment::create([
            'content' => $request->input('content'),
            'ID_Product' => $request->input('ID_Product'),
            'ID_User' => session('id_user'),
            'reply_to' => $request->input('reply_to'),
        ]);

        return json_encode([
            'status' => 200,
            'message' => 'commented'
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(comment $comment)
    {
        //
    }
}
