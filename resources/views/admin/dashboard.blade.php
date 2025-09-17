<x-layout>
    <div class="container-fluid p-5 bg-secondary-subtle text-center">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="display-1">Welcome back, admin: {{Auth::user()->name}}</h1>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2>Admin role requests</h2>
                <x-requests-table :roleRequests="$adminRequests" role="admin"/>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2>Revisor role requests</h2>
                <x-requests-table :roleRequests="$revisorRequests" role="revisor"/>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2>Writer role requests</h2>
                <x-requests-table :roleRequests="$writerRequests" role="writer"/>
            </div>
        </div>
    </div>

    <hr>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <h2>Tags</h2>
                    <form action="{{route('admin.storeTag')}}" method="POST" class="w-50 d-flex m-3">
                        @csrf
                        <input type="text" name="name" class="form-control me-2" placeholder="Insert new tag">
                        <button type="submit" class="btn btn-outline-secondary">Add</button>
                    </form>
                </div>
                <x-metainfo-table :metaInfos="$tags" metaType="tags"/>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <h2>Categories</h2>
                    <form action="{{route('admin.storeCategory')}}" method="POST" class="w-50 d-flex m-3">
                        @csrf
                        <input type="text" name="name" class="form-control me-2" placeholder="Insert new category">
                        <button type="submit" class="btn btn-outline-secondary">Add</button>
                    </form>
                </div>
                <x-metainfo-table :metaInfos="$categories" metaType="categorie"/>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">

                <h2>Financial Data</h2>

                @php($financeUsers = data_get($financialData, 'users', []))

                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer Name</th>
                            <th>Account Balance</th>
                            <th>Latest transactions</th>
                            <th>Credit Card Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($financeUsers))
                            <tr>
                                <td colspan="4">Financial service unavailable or no data.</td>
                            </tr>
                        @else
                            @foreach($financeUsers as $user)
                                <tr>
                                    <td>{{ $user['username'] ?? 'N/A' }}</td>
                                    <td>{{ isset($user['account_balance']) ? number_format($user['account_balance'], 2) : 'N/A' }}</td>
                                    <td>
                                        @if (!empty($user['transactions']))
                                            <ul class="mb-0">
                                                @foreach($user['transactions'] as $transaction)
                                                    <li>
                                                        {{ $transaction['date'] ?? '' }}:
                                                        {{ $transaction['description'] ?? '' }}
                                                        ({{ $transaction['amount'] ?? '' }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @php($cc = $user['credit_card'] ?? null)
                                        @if ($cc)
                                            <p class="mb-0">Card number: {{ $cc['card_number'] ?? 'N/A' }}</p>
                                            <p class="mb-0">Expire date: {{ $cc['expiry_date'] ?? 'N/A' }}</p>
                                            <p class="mb-0">CVV: {{ $cc['cvv'] ?? 'N/A' }}</p>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-layout>
