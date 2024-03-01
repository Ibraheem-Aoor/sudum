@extends(getTemplate() . '.layouts.app')
@section('content')
    <div class="payment-container" style=" margin: auto; margin-top: 50px; padding: 20px; box-sizing: border-box; overflow: hidden;">
        <h1 class="text-secondary font-weight-bold text-center" style="margin-bottom: 30px;">الدفع بواسطة  هايبر باي</h1>
        <form action="{{ route('payment_verify', ['gateway' => 'Hyperpay']) }}" class="paymentWidgets" data-brands="MADA VISA MASTER"></form>


    </div>
@endsection

<script>
    var wpwlOptions = {
        style: "card",
        brandDetection: true
    }
</script>

<script src="https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId={{ $checkoutId }}"></script>



{{-- =================================== --}}
{{-- <style>
    h4.payHead {
        font-family: Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        font-size: 14px;
        color: #09f;
        border: 2px solid #09f;
        border-radius: 4px;
        padding: 5px 8px;
        cursor: pointer;
    }

    .wpwl-container {
        display: none;
    }
</style> --}}





