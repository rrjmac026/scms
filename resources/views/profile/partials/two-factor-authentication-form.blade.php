<section>
    <header>
        <h2 class="text-2xl font-semibold text-[#FF92C2]">
            Two Factor Authentication
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Add additional security to your account using two factor authentication.
        </p>
    </header>

    @if(!auth()->user()->two_factor_secret)
        <form method="POST" action="{{ route('profile.enableTwoFactor') }}" class="mt-6">
            @csrf
            <button type="submit" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                Enable Two-Factor
            </button>
        </form>
    @else
        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-[#FFC8FB]/30">
            <p class="text-sm text-gray-600 mb-4">
                Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
            </p>

            <div class="mb-4 p-4 bg-white rounded-lg inline-block">
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            </div>

            <form method="POST" action="{{ route('profile.disableTwoFactor') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Disable Two-Factor
                </button>
            </form>
        </div>
    @endif
</section>

