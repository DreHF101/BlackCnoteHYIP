import React, { useState, useEffect } from 'react';
import { 
  ArrowUpRight, 
  ArrowDownLeft, 
  DollarSign, 
  Calendar, 
  Download,
  RefreshCw,
  AlertCircle,
  Search,
  Eye,
  EyeOff
} from 'lucide-react';
import type { BlackCnoteApiSettings } from '../config/environment';

interface Transaction {
  id: number;
  type: string;
  amount: number;
  description: string;
  date: string;
  status: string;
}

// Mock data for development when backend is not available
const mockTransactions: Transaction[] = [
  {
    id: 1,
    type: 'deposit',
    amount: 10000.00,
    description: 'Initial deposit',
    date: '2024-01-10T10:30:00Z',
    status: 'completed'
  },
  {
    id: 2,
    type: 'investment',
    amount: -5000.00,
    description: 'Premium Plan investment',
    date: '2024-01-15T14:20:00Z',
    status: 'completed'
  },
  {
    id: 3,
    type: 'profit',
    amount: 125.00,
    description: 'Daily profit from Premium Plan',
    date: '2024-01-16T09:15:00Z',
    status: 'completed'
  },
  {
    id: 4,
    type: 'profit',
    amount: 125.00,
    description: 'Daily profit from Premium Plan',
    date: '2024-01-17T09:15:00Z',
    status: 'completed'
  },
  {
    id: 5,
    type: 'withdrawal',
    amount: -1000.00,
    description: 'Bank transfer withdrawal',
    date: '2024-01-18T16:45:00Z',
    status: 'completed'
  },
  {
    id: 6,
    type: 'profit',
    amount: 54.00,
    description: 'Daily profit from Standard Plan',
    date: '2024-01-19T09:15:00Z',
    status: 'completed'
  }
];

const Transactions = () => {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filter, setFilter] = useState('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [showAmounts, setShowAmounts] = useState(true);

  // Fetch transactions from WordPress REST API
  useEffect(() => {
    const fetchTransactions = async () => {
      try {
        setLoading(true);
        
        // Check if we're in development mode first
        const isDevelopment = process.env.NODE_ENV === 'development' || window.location.hostname === 'localhost';
        
        if (isDevelopment) {
          // Use mock data for development
          return;
        }
        
        const apiSettings = (window as { blackCnoteApiSettings?: BlackCnoteApiSettings }).blackCnoteApiSettings;
        if (!apiSettings) {
          throw new Error('API settings not found');
        }

        // Check if we're in development mode using the new flag
        if (process.env.NODE_ENV === 'development') {
          // Use mock data for development
          setTransactions(mockTransactions);
        } else {
          // Try to fetch real data from WordPress backend
          try {
            const response = await fetch(`${apiSettings?.baseUrl ?? ''}transactions`, {
              headers: {
                'X-WP-Nonce': apiSettings.nonce || '',
              }
            });

            if (response.ok) {
              const data = await response.json();
              setTransactions(data);
            } else if (response.status === 401) {
              // User not logged in, redirect to login
              window.location.href = '/wp-login.php';
              return;
            } else {
              throw new Error('Failed to fetch transactions');
            }
          } catch (apiError) {
            if (process.env.NODE_ENV === 'development') console.warn('API call failed, falling back to mock data:', apiError);
            // Fallback to mock data if API call fails
            setTransactions(mockTransactions);
          }
        }

      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to load transactions');
      } finally {
        setLoading(false);
      }
    };

    fetchTransactions();
  }, []);

  const filteredTransactions = transactions.filter(transaction => {
    const matchesFilter = filter === 'all' || transaction.type === filter;
    const matchesSearch = transaction.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         transaction.amount.toString().includes(searchTerm) ||
                         transaction.date.includes(searchTerm);
    return matchesFilter && matchesSearch;
  });

  const getTransactionIcon = (type: string) => {
    switch (type) {
      case 'deposit':
        return <ArrowUpRight className="h-5 w-5 text-green-600" />;
      case 'withdrawal':
        return <ArrowDownLeft className="h-5 w-5 text-red-600" />;
      case 'profit':
        return <DollarSign className="h-5 w-5 text-blue-600" />;
      default:
        return <DollarSign className="h-5 w-5 text-gray-600" />;
    }
  };

  const getTransactionColor = (type: string) => {
    switch (type) {
      case 'deposit':
        return 'bg-green-100';
      case 'withdrawal':
        return 'bg-red-100';
      case 'profit':
        return 'bg-blue-100';
      default:
        return 'bg-gray-100';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 text-green-800';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800';
      case 'failed':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const exportTransactions = () => {
    const csvContent = [
      ['Date', 'Type', 'Description', 'Amount', 'Status'],
      ...filteredTransactions.map(t => [
        formatDate(t.date),
        t.type,
        t.description,
        t.amount.toString(),
        t.status
      ])
    ].map(row => row.join(',')).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `transactions-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  const refreshData = () => {
    window.location.reload();
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <RefreshCw className="h-8 w-8 text-yellow-500 animate-spin mx-auto mb-4" />
          <p className="text-gray-600">Loading transactions...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <AlertCircle className="h-8 w-8 text-red-500 mx-auto mb-4" />
          <p className="text-red-600 mb-4">{error}</p>
          <button 
            onClick={refreshData}
            className="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Transaction History</h1>
            <p className="text-gray-600 mt-1">View all your deposits, withdrawals, and profits.</p>
          </div>
          <div className="flex space-x-4 mt-4 sm:mt-0">
            <button
              onClick={() => setShowAmounts(!showAmounts)}
              className="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2"
            >
              {showAmounts ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
              <span>{showAmounts ? 'Hide' : 'Show'} Amounts</span>
            </button>
            <button
              onClick={exportTransactions}
              className="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center space-x-2"
            >
              <Download className="h-4 w-4" />
              <span>Export CSV</span>
            </button>
          </div>
        </div>

        {/* Filters and Search */}
        <div className="bg-white rounded-2xl shadow-lg p-6 mb-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            {/* Search */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                type="text"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                placeholder="Search transactions..."
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              />
            </div>

            {/* Filter */}
            <div>
              <select
                value={filter}
                onChange={(e) => setFilter(e.target.value)}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
              >
                <option value="all">All Transactions</option>
                <option value="deposit">Deposits</option>
                <option value="withdrawal">Withdrawals</option>
                <option value="profit">Profits</option>
              </select>
            </div>

            {/* Summary */}
            <div className="text-right">
              <div className="text-sm text-gray-600">Total Transactions</div>
              <div className="text-2xl font-bold text-gray-900">{filteredTransactions.length}</div>
            </div>
          </div>
        </div>

        {/* Transactions List */}
        <div className="bg-white rounded-2xl shadow-lg overflow-hidden">
          {filteredTransactions.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Transaction
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Description
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {filteredTransactions.map((transaction) => (
                    <tr key={transaction.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className={`w-10 h-10 rounded-full flex items-center justify-center ${getTransactionColor(transaction.type)}`}>
                            {getTransactionIcon(transaction.type)}
                          </div>
                          <div className="ml-4">
                            <div className="text-sm font-medium text-gray-900 capitalize">
                              {transaction.type}
                            </div>
                            <div className="text-sm text-gray-500">
                              ID: {transaction.id}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <div className="text-sm text-gray-900">{transaction.description}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className={`text-sm font-semibold ${
                          transaction.type === 'deposit' || transaction.type === 'profit' 
                            ? 'text-green-600' 
                            : 'text-red-600'
                        }`}>
                          {showAmounts ? (
                            <>
                              {transaction.type === 'deposit' || transaction.type === 'profit' ? '+' : '-'}
                              ${transaction.amount.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                            </>
                          ) : (
                            '••••••'
                          )}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-900">{formatDate(transaction.date)}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(transaction.status)}`}>
                          {transaction.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center py-12">
              <DollarSign className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-gray-600 mb-4">No transactions found</p>
              {searchTerm || filter !== 'all' ? (
                <button
                  onClick={() => {
                    setSearchTerm('');
                    setFilter('all');
                  }}
                  className="text-yellow-600 hover:text-yellow-700 font-medium"
                >
                  Clear filters
                </button>
              ) : (
                <p className="text-sm text-gray-500">Your transaction history will appear here</p>
              )}
            </div>
          )}
        </div>

        {/* Summary Cards */}
        {filteredTransactions.length > 0 && (
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            <div className="bg-white rounded-2xl shadow-lg p-6">
              <div className="flex items-center">
                <div className="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center">
                  <ArrowUpRight className="h-6 w-6 text-green-600" />
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-600">Total Deposits</div>
                  <div className="text-2xl font-bold text-gray-900">
                    ${filteredTransactions
                      .filter(t => t.type === 'deposit')
                      .reduce((sum, t) => sum + t.amount, 0)
                      .toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-2xl shadow-lg p-6">
              <div className="flex items-center">
                <div className="bg-red-100 w-12 h-12 rounded-lg flex items-center justify-center">
                  <ArrowDownLeft className="h-6 w-6 text-red-600" />
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-600">Total Withdrawals</div>
                  <div className="text-2xl font-bold text-gray-900">
                    ${filteredTransactions
                      .filter(t => t.type === 'withdrawal')
                      .reduce((sum, t) => sum + t.amount, 0)
                      .toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-2xl shadow-lg p-6">
              <div className="flex items-center">
                <div className="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center">
                  <DollarSign className="h-6 w-6 text-blue-600" />
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-600">Total Profits</div>
                  <div className="text-2xl font-bold text-gray-900">
                    ${filteredTransactions
                      .filter(t => t.type === 'profit')
                      .reduce((sum, t) => sum + t.amount, 0)
                      .toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-2xl shadow-lg p-6">
              <div className="flex items-center">
                <div className="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center">
                  <Calendar className="h-6 w-6 text-yellow-600" />
                </div>
                <div className="ml-4">
                  <div className="text-sm font-medium text-gray-600">Net Balance</div>
                  <div className="text-2xl font-bold text-gray-900">
                    ${(
                      filteredTransactions
                        .filter(t => t.type === 'deposit' || t.type === 'profit')
                        .reduce((sum, t) => sum + t.amount, 0) -
                      filteredTransactions
                        .filter(t => t.type === 'withdrawal')
                        .reduce((sum, t) => sum + t.amount, 0)
                    ).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default Transactions; 
