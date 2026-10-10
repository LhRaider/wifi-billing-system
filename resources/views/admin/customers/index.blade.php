<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-heading">
            <div class="admin-page-heading__icon">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h2>Customer Management</h2>
                <p>Manage customers belonging to your ISP account.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .customers-page {
            --page-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text: #111827;
            --muted: #64748b;
            --border: #e2e8f0;
            --table-head: #f8fafc;
            --input-bg: #ffffff;
            --input-text: #111827;
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text);
        }

        html.dark .customers-page {
            --page-bg: #091226;
            --card-bg: #101c33;
            --text: #e8eeff;
            --muted: #94a3b8;
            --border: #293a59;
            --table-head: #15233e;
            --input-bg: #0b172b;
            --input-text: #f8fafc;
        }

        .admin-page-heading {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-page-heading__icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: #2563eb;
            color: white;
        }

        .admin-page-heading h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .admin-page-heading p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .admin-page-heading h2 {
            color: #f8fafc;
        }

        html.dark .admin-page-heading p {
            color: #94a3b8;
        }

        .customers-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, .08);
        }

        .customers-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .customers-title {
            margin: 0;
            color: var(--text);
            font-size: 22px;
            font-weight: 800;
        }

        .customers-subtitle {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .record-badge {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 800;
        }

        html.dark .record-badge {
            background: #172d55;
            color: #93c5fd;
        }

        .customers-search {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .customers-search input {
            flex: 1;
            min-width: 0;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--input-bg);
            color: var(--input-text);
            outline: none;
        }

        .customers-search input::placeholder {
            color: var(--muted);
        }

        .customers-search input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .14);
        }

        .search-btn {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
        }

        .customers-table-wrap {
            overflow-x: auto;
        }

        .customers-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        .customers-table th,
        .customers-table td {
            border: 1px solid var(--border);
            padding: 12px;
            text-align: left;
            color: var(--text);
        }

        .customers-table th {
            background: var(--table-head);
            font-weight: 800;
        }

        .customers-table tbody tr {
            background: var(--card-bg);
        }

        .customers-table tbody tr:hover {
            background: rgba(59, 130, 246, .04);
        }

        html.dark .customers-table tbody tr:hover {
            background: #14223d;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-suspended {
            background: #fee2e2;
            color: #991b1b;
        }

        html.dark .status-active {
            background: #123c2a;
            color: #86efac;
        }

        html.dark .status-suspended {
            background: #451d29;
            color: #fda4af;
        }

        .action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .action-row form {
            margin: 0;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 13px;
            border: none;
            border-radius: 7px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-view { background: #2563eb; }
        .btn-activate { background: #16a34a; }
        .btn-suspend { background: #dc2626; }

        .customer-note {
            margin-top: 24px;
            padding: 14px 16px;
            background: #eff6ff;
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.6;
        }

        html.dark .customer-note {
            background: #10264b;
            color: #bfdbfe;
            border-color: #264d83;
        }

        .customers-pagination {
            margin-top: 20px;
            color: var(--text);
        }

        @media (max-width: 640px) {
            .customers-page {
                padding-top: 24px !important;
            }

            .customers-card {
                padding: 16px;
                border-radius: 12px;
            }

            .customers-search {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
            }
        }
    </style>

    <div class="customers-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="customers-card">

                <div class="customers-title-row">
                    <div>
                        <h3 class="customers-title">Customers</h3>
                        <p class="customers-subtitle">
                            Manage customers belonging to your ISP account.
                        </p>
                    </div>

                    <span class="record-badge">
                        {{ $customers->total() }} Records
                    </span>
                </div>

                <form
                    method="GET"
                    action="{{ route('admin.customers.index') }}"
                    class="customers-search"
                >
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        maxlength="100"
                        placeholder="Search customer by name or email..."
                    >

                    <button type="submit" class="search-btn">
                        Search
                    </button>
                </form>

                <div class="customers-table-wrap">
                    <table class="customers-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <strong>{{ $customer->name }}</strong>
                                    </td>

                                    <td>{{ $customer->email }}</td>

                                    <td>
                                        @if($customer->is_suspended)
                                            <span class="status-badge status-suspended">
                                                Suspended
                                            </span>
                                        @else
                                            <span class="status-badge status-active">
                                                Active
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $customer->created_at->format('d M Y') }}
                                    </td>

                                    <td>
                                        <div class="action-row">
                                            <a
                                                href="{{ route('admin.customers.show', $customer) }}"
                                                class="action-btn btn-view"
                                            >
                                                View
                                            </a>

                                            @if($customer->is_suspended)
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.customers.activate', $customer) }}"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="action-btn btn-activate"
                                                    >
                                                        Activate
                                                    </button>
                                                </form>
                                            @else
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.customers.suspend', $customer) }}"
                                                    onsubmit="return confirm('Suspend this customer and remove their current service access?');"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="action-btn btn-suspend"
                                                    >
                                                        Suspend
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:30px;">
                                        No customers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="customers-pagination">
                    {{ $customers->links() }}
                </div>

                <div class="customer-note">
                    Suspending a customer immediately removes local service entitlement
                    and attempts to disable their associated MikroTik access. Reactivating
                    the customer account does not automatically restore internet service;
                    a valid suspended subscription must be activated separately.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>