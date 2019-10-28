<div class="card">
    <div class="card-block" style="min-height: 0px; display: flex; justify-content: center; align-items: center;">
        <style>

            .my-bids {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 50%;
                margin: 15px;
                padding: 10px;
                border-radius: .1875rem;
                background-color: #fa7a50;
            }

            .my-bids a {
                color: #fff;
                text-decoration: none;
                text-align: center;
            }
        </style>

        <div class="form-group text-right btn-primary my-bids">
            <a href="{{ route('frontend.flights.bids') }}">{{ trans_choice('flights.mybid', 2) }}</a>
        </div>
    </div>
</div>