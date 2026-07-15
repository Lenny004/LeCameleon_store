<ol class="order-timeline checkout-steps" aria-label="Order status">
    @foreach ($timeline as $step)
        <li class="checkout-step {{ $step['current'] ? 'checkout-step--active' : ($step['completed'] ? 'checkout-step--done' : '') }}">
            <span class="checkout-step__num">{{ $loop->iteration }}</span>
            {{ $step['label'] }}
        </li>
    @endforeach
</ol>
