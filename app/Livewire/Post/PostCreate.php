<?php

namespace App\Livewire\Post;

use Livewire\Component;
use Flux\Flux;
use App\Models\Post;

class PostCreate extends Component
{
    public $title;
    public $content;
    public $author;

    protected function rules()
    {
        return [
            'title' => 'required|string|unique:posts,title|max:255', // Increased max length
            'content' => 'required|string',
            'author' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();

        Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author,
        ]);

        // Close the modal (if Flux modal is being used)
        Flux::modal('create-post')->close();

        // Emit an event to notify other components
        $this->dispatch('post-created');

        // Reset the form fields after saving
        $this->reset(['title', 'content', 'author']);
    }

    public function render()
    {
        return view('livewire.post.post-create');
    }
}