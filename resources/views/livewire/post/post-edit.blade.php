<div>
    <flux:modal name="edit-post-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Post</flux:heading>
                <flux:text class="mt-2">Update your post details below.</flux:text>
            </div>

            <flux:input label="Title" wire:model="title" />

            <flux:input label="Author" wire:model="author" />

            <flux:textarea label="Content" wire:model="content" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click="updatePost">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>