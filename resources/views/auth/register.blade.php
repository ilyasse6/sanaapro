<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name and Prenom -->
        <div class="flex" style="margin-bottom: 20px">
            <div class="w-1/2 mr-2" style="margin-right: 20px">
                <x-input-label for="nom" :value="__('nom')" />
                <x-text-input id="nom" class="block mt-1 w-full" type="text" name="nom" :value="old('nom')" required autofocus autocomplete="nom" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>
            <div class="w-1/2">
                <x-input-label for="prenom" :value="__('prenom')" />
                <x-text-input id="prenom" class="block mt-1 w-full" type="text" name="prenom" :value="old('prenom')" required autofocus autocomplete="prenom" />
                <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
            </div>
        </div>

        <!-- Organisme -->
        {{-- <div>
            <x-input-label for="etablissement" :value="__('Select etablissement')" />
            <select id="etablissement" name="etablissement" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">-- Select a Hospital --</option>
                <option value="Dchuis">Dchuis</option>
                <option value="Moulay Abdellah">Moulay Abdellah</option>
                <option value="Moulay Youssef">Moulay Youssef</option>
                <option value="Lalla Aicha">Lalla Aicha</option>
                <option value="Her">HER</option>
                <option value="Hsr">HSR</option>
                <option value="Cmp">CMP</option>
                <option value="Azemour">AZEMOUR</option>
            </select>
            <x-input-error :messages="$errors->get('etablissement')" class="mt-2" />
        </div> --}}
        <div>
            <x-input-label for="entreprise_id" :value="__('Select etablissement')" />
            <select id="entreprise_id" name="entreprise_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">-- Select a Hospital --</option>
                @foreach($hopitals as $hopital)
                    <option value="{{ $hopital->idh }}">{{ $hopital->abreviation }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('entreprise_id')" class="mt-2" />
        </div>
        <!-- Poste -->
        <div class="mt-4">
            <x-input-label for="poste" :value="__('Select Poste')" />
            <select id="poste" name="poste" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">-- Select a Poste --</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->id }}">{{ $poste->intitule }}</option>
                @endforeach
                {{-- <option value="Hépato-gastroentérologue">Hépato-gastroentérologue</option>
                <option value="Réanimateur anesthésiste">Réanimateur anesthésiste </option>
                <option value="Chirurgien viscéraliste">Chirurgien viscéraliste</option> --}}
            </select>
            <x-input-error :messages="$errors->get('poste')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4" style="margin-bottom: 20px">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password and Confirm Password -->
        <div class="flex">
            <div class="w-1/2 mr-2" style="margin-right: 20px">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="w-1/2">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
