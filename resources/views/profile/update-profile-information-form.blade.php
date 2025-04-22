<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input 
                type="file" 
                id="photo" 
                class="hidden" 
                wire:model="photo" 
                x-ref="photo" 
                x-on:change="
                    photoName = $refs.photo.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        photoPreview = e.target.result;
                    };
                    reader.readAsDataURL($refs.photo.files[0]);
                " 
            />

            <x-label for="photo" value="{{ __('Photo') }}" style="color: black;" />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
            @if (Auth::user()->profile_photo_path)
                <img src="{{ asset('public/storage/' . Auth::user()->profile_photo_path) }}" alt="Profile Picture" width="150" height="150" class="rounded-circle">
            @else
                <img src={{ asset ('public/default-profile.jpg')}} alt="Default Profile" width="150" height="150" class="rounded-circle">
            @endif
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
                <span 
                    class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center" 
                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                >
                </span>
            </div>

            <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()" style="color: black;">
                {{ __('Select A New Photo') }}
            </x-secondary-button>

            @if ($this->user->profile_photo_path)
                <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto" style="color: black;">
                    {{ __('Remove Photo') }}
                </x-secondary-button>
            @endif

            <x-input-error for="photo" class="mt-2" style="color: black;" />
        </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" class="dark-mode-text" />
            <x-input id="name" type="text" class="mt-1 block w-full dark-mode-text" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2 dark-mode-text" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" class="dark-mode-text" />
            <x-input id="email" type="email" class="mt-1 block w-full dark-mode-text" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2 dark-mode-text" />

            {{-- @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2" style="color: black;">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification" style="color: black;">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600" style="color: black;">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif --}}
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
