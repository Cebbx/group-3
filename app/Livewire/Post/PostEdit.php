<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\Rule;

class PostEdit extends Component
{
    public Post $post;
    public $title;
    public $content;
    public $author;

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:10',
                Rule::unique('posts', 'title')->ignore($this->post?->id),
            ],
            'content' => 'required|string',
            'author' => 'required|string',
        ];
    }

    #[On('edit-post')]
    public function loadPost($id)
    {
        $this->post = Post::findOrFail($id);
        $this->title = $this->post->title;
        $this->content = $this->post->content;
        $this->author = $this->post->author;

        Flux::modal('edit-post-modal')->show();
    }

    public function updatePost()
    {
        $this->validate();

        $this->post->update([
            'title' => $this->title,
            'author' => $this->author,
            'content' => $this->content,
        ]);

        Flux::modal('edit-post-modal')->close();
        $this->dispatch('post-updated');
    }

    public function render()
    {
        return view('livewire.post.post-edit');
    }
}
