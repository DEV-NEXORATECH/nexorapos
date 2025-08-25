<?php
namespace App\Services\Admin;

use App\Validations\Admin\Post\PostStoreValidation;
use App\Validations\Admin\Post\PostUpdateValidation;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use DataTables;

class PostService
{
    public $postStoreValidation;
    public $postUpdateValidation;

    public function __construct()
    {
        $this->postStoreValidation = new PostStoreValidation();
        $this->postUpdateValidation = new PostUpdateValidation();
    }

    // Index method to handle both page load and AJAX request for DataTables
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $posts = Post::with('users', 'categories')->select('posts.*');

            return DataTables::of($posts)
                ->addColumn('user_name', function ($post) {
                    return $post->users->name;
                })
                ->addColumn('category_name', function ($post) {
                    return $post->categories->name;
                })
                ->addColumn('content', function ($post) {
                    return \Str::limit($post->content, 50);
                })
                ->addColumn('image', function ($post) {
                    return '<img src="' . asset('admin/uploads/' . $post->image) . '" width="50" height="50" />';
                })
                ->addColumn('action', function ($post) {
                    $editUrl = url('admin-panel/posts/edit', $post->id);
                    $deleteUrl = url('admin-panel/posts/delete', $post->id);
                    return '
                        <a href="' . $editUrl . '" class="btn btn-success me-2"><i class="fas fa-edit"></i></a>
                        <a href="' . $deleteUrl . '" class="btn btn-danger delete-category"><i class="fas fa-trash"></i></a>
                    ';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        // For normal page load
        $posts = Post::with('users', 'categories')->get();
        return view('admin.posts.listing', compact('posts'));
    }

    public function post()
    {
        $users = User::all();
        $categories = Category::all();
        return view('admin.posts.add', compact('users', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->postStoreValidation->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $post = new Post();
            $post->user_id = $request->user_id;
            $post->category_id = $request->category_id;
            $post->title = $request->title;
            $post->content = $request->content;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('admin/uploads'), $imageName);
                $post->image = $imageName;
            }

            $saved = $post->save();

            if ($saved) {
                return redirect()->to('admin-panel/posts/listing')->with('success', 'Post Uploaded Successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something Went Wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $post = Post::find($id);
            $post->delete();

            if ($post) {
                return redirect()->to('admin-panel/posts/listing')->with('success', 'Post Deleted Successfully');
            } else {
                return redirect()->back()->with('failed', 'Something Went Wrong');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $users = User::all();
        $categories = Category::all();
        $posts = Post::find($id);
        if ($posts) {
            return view('admin.posts.update', compact('posts', 'users', 'categories'));
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::find($id);

            if ($post) {
                $inputs = $request->all();
                $errorMessage = $this->postUpdateValidation->validator($inputs);
                if ($errorMessage) {
                    return redirect()->back()->with('failed', $errorMessage);
                }

                $post->user_id = $request->user_id;
                $post->category_id = $request->category_id;
                $post->title = $request->title;
                $post->content = $request->content;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('admin/uploads'), $imageName);
                    $post->image = $imageName;
                }

                $post->save();

                return redirect()->to('admin-panel/posts/listing')->with('success', 'Post updated successfully');
            } else {
                return redirect()->back()->with('failed', 'Post not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
