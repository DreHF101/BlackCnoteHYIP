import React, { useState, useEffect } from 'react';
import { hyiplabAPI, User, Investment, Transaction } from '../../api/hyiplab';
import { Stats } from './Stats';
import { QuickActions } from './QuickActions';
import './Dashboard.css';

export const Dashboard: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);
  const [investments, setInvestments] = useState<Investment[]>([]);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);
        const [userData, investmentsData, transactionsData] = await Promise.all([
          hyiplabAPI.getUserData(),
          hyiplabAPI.getUserInvestments(),
          hyiplabAPI.getUserTransactions(),
        ]);

        setUser(userData);
        setInvestments(investmentsData);
        setTransactions(transactionsData);
        setError(null);
      } catch (err) {
        console.error('Error fetching dashboard data:', err);
        setError('Failed to load dashboard data. Please try again later.');
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  if (loading) {
    return (
      <div className="dashboard-loading">
        <div className="loading-spinner"></div>
        <p>Loading your dashboard...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="dashboard-error">
        <p>{error}</p>
        <button onClick={() => window.location.reload()}>Retry</button>
      </div>
    );
  }

  if (!user) {
    return (
      <div className="dashboard-error">
        <p>User data not available. Please log in again.</p>
      </div>
    );
  }

  const activeInvestments = investments.filter(inv => inv.status === 'active');
  const totalInvested = investments.reduce((sum, inv) => sum + inv.amount, 0);
  const recentTransactions = transactions.slice(0, 5);

  return (
    <div className="dashboard-container">
      <div className="dashboard-header">
        <h1>Welcome back, {user.firstName}!</h1>
        <p>Here's your investment overview</p>
      </div>

      <div className="dashboard-content">
        <div className="dashboard-main">
          <Stats 
            balance={user.balance}
            totalInvested={totalInvested}
            activeInvestments={activeInvestments.length}
            totalTransactions={transactions.length}
          />

          <QuickActions />

          <div className="dashboard-section">
            <h3>Recent Transactions</h3>
            {recentTransactions.length > 0 ? (
              <div className="transactions-list">
                {recentTransactions.map(transaction => (
                  <div key={transaction.id} className="transaction-item">
                    <div className="transaction-info">
                      <span className="transaction-type">{transaction.type}</span>
                      <span className="transaction-remark">{transaction.remark}</span>
                      <span className="transaction-date">
                        {new Date(transaction.created_at).toLocaleDateString()}
                      </span>
                    </div>
                    <div className="transaction-amount">
                      <span className={`amount ${transaction.type === 'investment' ? 'negative' : 'positive'}`}>
                        {transaction.type === 'investment' ? '-' : '+'}
                        ${transaction.amount.toFixed(2)}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="no-transactions">No transactions yet.</p>
            )}
          </div>
        </div>

        <div className="dashboard-sidebar">
          <div className="dashboard-section">
            <h3>Active Investments</h3>
            {activeInvestments.length > 0 ? (
              <div className="investments-list">
                {activeInvestments.map(investment => (
                  <div key={investment.id} className="investment-item">
                    <div className="investment-info">
                      <span className="investment-plan">
                        {investment.plan?.name || `Plan #${investment.plan_id}`}
                      </span>
                      <span className="investment-amount">
                        ${investment.amount.toFixed(2)}
                      </span>
                    </div>
                    <div className="investment-status">
                      <span className="status-badge active">Active</span>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="no-investments">No active investments.</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}; 