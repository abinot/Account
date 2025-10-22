<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Modules\Auth\Models\AuthUserMeta;
use Modules\Auth\Models\OTP;

new #[Layout("components.layouts.auth")] class extends Component {
    public string $phone = "";
    public string $otp = "";
    public bool $otpSent = false;

    /**
     * مرحله ۱: ارسال OTP
     */
    public function sendOtp(): void
    {
        $this->validate([
            "phone" => ["required", 'regex:/^09\d{9}$/'],
        ]);

        $this->phone = trim($this->phone);

        // بررسی اینکه شماره قبلاً ثبت نشده باشد
        $exists = AuthUserMeta::where("key", "phone")
            ->where("value", $this->phone)
            ->exists();

        if ($exists) {
            $this->addError("phone", "این شماره تلفن قبلاً ثبت شده است.");
            return;
        }

        // تولید OTP
        $otp = (string) rand(1000000, 9999999);

        // ذخیره در جدول OTP
        OTP::updateOrCreate(["phone" => $this->phone], ["otp" => $otp, "expires_at" => now()->addMinutes(2)]);


        $this->otpSent = true;

        // نمایش OTP روی صفحه (حالت تست)
        session()->flash("status", "کد تایید شما: $otp");
    }

    /**
     * مرحله ۲: بررسی OTP و ساخت کاربر
     */
    public function verifyOtp(): void
    {
        $this->validate([
            "otp" => ["required", "digits:7"],
        ]);

        $this->phone = trim($this->phone);

        $record = OTP::where("phone", $this->phone)
            ->where("otp", (string) $this->otp)
            ->where("expires_at", ">", now())
            ->first();

        if (! $record) {
            $this->addError("otp", "کد وارد شده صحیح یا منقضی شده است.");
            return;
        }

        // ساخت رمز تصادفی
        $randomPassword = Str::random(7);

        // ساخت کاربر
        $user = User::create([
            "name" => $this->phone,
            "email" => "user" . $this->phone . "@seamelk.ir",
            "password" => Hash::make($randomPassword),
        ]);

        $user->update(["email" => $user->id . "@seamelk.ir"]);

        // ذخیره متاها
        AuthUserMeta::create([
            "user_id" => $user->id,
            "key"     => "internal_email",
            "value"   => $user->id . "@seamelk.ir",
        ]);

        AuthUserMeta::create([
            "user_id" => $user->id,
            "key"     => "phone",
            "value"   => $this->phone,
        ]);

        AuthUserMeta::create([
            "user_id" => $user->id,
            "key"     => "first_password",
            "value"   => $randomPassword,
        ]);

        session()->flash("status", "رمز  شما: $randomPassword");
        dd($randomPassword);
        // حذف OTP بعد از استفاده
        $record->delete();

        event(new Registered($user));
        Auth::login($user);

        session()->regenerate();


        $this->redirectIntended(route("dashboard", absolute: false), navigate: true);
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header title="ثبت‌نام" description="سی ملک : دریایی از خانه ها" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    @if (! $otpSent)
        {{-- مرحله اول: دریافت شماره تلفن --}}
        <form wire:submit="sendOtp" class="flex flex-col gap-6">
            <flux:input wire:model="phone" label="شماره تلفن" type="tel" required placeholder="09** *** ****" />
            <flux:button type="submit" variant="primary" class="w-full">ارسال کد تایید</flux:button>
        </form>
    @else
        {{-- مرحله دوم: دریافت OTP --}}
        <form wire:submit="verifyOtp" class="flex flex-col gap-6">
            <flux:input wire:model="phone" label="شماره تلفن" type="tel" readonly disabled />

            <flux:input wire:model="otp" label="کد تایید" type="text" required placeholder="*******" />

            <flux:button type="submit" variant="primary" class="w-full">تایید و ثبت‌نام</flux:button>
        </form>
    @endif

    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>قبلاً حساب دارید؟</span>
        <flux:link :href="route('login')" wire:navigate>ورود</flux:link>
    </div>
</div>

