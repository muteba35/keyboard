<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __("Nous avons envoyé un code de vérification (OTP) à votre adresse e-mail. Veuillez entrer ce code pour confirmer votre identité. Si vous n'avez pas reçu l'e-mail, vous pouvez demander un nouvel envoi.") }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
        {{ __('Un nouvel Otp a été envoyé à votre adresse email ou votre numero de telephone.')}}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('otp.verify')}}">
        @csrf
        <div>
            <x-input-label for="otp" value="Code OTP" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>
        <div class="mt-4 flex items-center justify-between">
                <x-primary-button>
                    {{ __('Confirm Email-OTP')}}
                </x-primary-button>
         </div>
         </form>
         </div>
    
    

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('otp.resend')}}">
            @csrf
      
            <div>
                <x-primary-button>
                    {{ __('Resend Verification  Email-otp')}}
                </x-primary-button>
                
           
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
