{{-- <section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section> --}}
<section class="section">
    <form class="row justify-content-center" action="{{ route('password.update') }}" method="post">
        @csrf
        @method('put')
        <div class="col-lg-12">
            <div class="fade-in-left">
                <div class="card profile-card">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="bi bi-lock-fill me-1"></i>
                            <span>Modifier le mot de passe</span>
                        </h6>
                        <div class="row g-3">
                            <!-- current_password -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_password" class="form-label">
                                        <i class="bi bi-key-fill me-1"></i>Mot de passe actuel :
                                    </label>
                                    <input type="password" class="form-control custom-input" id="current_password"
                                        name="current_password" placeholder="Entrez votre mot de passe actuel" required>
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="text-danger mt-2" />
                                </div>
                            </div>

                            <!-- new_password -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock-fill me-1"></i>Nouveau mot de passe :
                                    </label>
                                    <input type="password" class="form-control custom-input" id="password"
                                        name="password" placeholder="Entrez un nouveau mot de passe" required>
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="text-danger mt-2" />
                                </div>
                            </div>

                            <!-- password_confirmation -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="bi bi-check-circle-fill me-1"></i>Confirmer le mot de passe :
                                    </label>
                                    <input type="password" class="form-control custom-input" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirmez votre nouveau mot de passe"
                                        required>
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="text-danger mt-2" />
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-md">
                                    <i class="ri-save-2-fill me-2"></i>Enregistrer
                                </button>
                            </div>
                            {{-- <div class="col-12">
                                @if (session('status') === 'password-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600 mt-2">{{ __('Mot de passe mis à jour.') }}</p>
                                @endif
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
