<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\Repositories\Post\IPost;
use App\Core\Repositories\Comment\IComment;
use App\Core\Repositories\Category\ICategory;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreCommentRequest;

use Gate;
use App\User;
use App\Models\Post;
use App\Models\Comment;

class PostController extends Controller
{
    protected $postRepository;
    protected $commentRepository;
    protected $categoryRepository;

    public function __construct(IPost $postRepository, IComment $commentRepository, ICategory $categoryRepository) {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->categoryRepository = $categoryRepository;
    }
    public function index() {
        $item = $this->postRepository->getAll()->sortByDesc('id');
        return $item;
    }
    public function show($slug) {
        $post = $this->postRepository->findBySlug($slug);
        $comments = $post->comment->sortByDesc('id');
        return view('pages.posts.post', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
    public function create() {
        $category = $this->categoryRepository->getAll();
        return view('pages.posts.createPost', ['category' => $category]);
    }

    public function store(StorePostRequest $request) {

        $validated = $request->validated();
        $post = $this->postRepository->create($request->all());
        $categories = $request->categorySelect;
        $post->category()->attach($categories);
        return redirect(route('home'));
         
    }
    public function edit($slug) {
        $post = $this->postRepository->findBySlug($slug);
        $categories = $this->categoryRepository->getAll();
        return view('pages.posts.editPost', ['post' => $post, 'categories' => $categories]);
    }
    public function update($id, Request $request) {
        $this->postRepository->update($id, $request->all());
        $post = $this->postRepository->find($id);
        $post->category()->sync($request->categorySelect);
        $slug = $post->slug;
        return redirect(route('post.show', $slug));
    }
    public function destroy($id) {
        $this->postRepository->delete($id);
        return redirect(route('home'));
    }

    public function comments() {
        $item = rsort($this->commentRepository->getAll());
        return $item;
    }

    public function storeComment(StoreCommentRequest $request) {
        $validated = $request->validated();
        
        $data = $request->all();
        $item = $this->commentRepository->create($data);
        $comments = $this->commentRepository->getAll();
        return redirect(route('post.show', $item->post_id));
    }

    public function updateComment($id, Request $request){

    }

    public function destroyComment($id) {
        $item = $id;
        $this->commentRepository->delete($id);
    }
}
