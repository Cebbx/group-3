<div>

<flux:modal.trigger name="create-post">
    <flux:button variant="primary" color="rose">Create Post</flux:button>
</flux:modal.trigger>

<flux:modal name="create-post" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Create Post</flux:heading>
            <flux:text class="mt-2">Post Now</flux:text>
        </div>

        <flux:input label="Title" placeholder="Your title" wire:model="title" />

        <flux:input label="Author" placeholder="Your name"wire:model="author" />

        <flux:textarea label="Content" placeholder="Place your content here"wire:model="content" />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary" wire:click="save" >Save changes</flux:button>
        </div>
    </div>
</flux:modal>

</div>
