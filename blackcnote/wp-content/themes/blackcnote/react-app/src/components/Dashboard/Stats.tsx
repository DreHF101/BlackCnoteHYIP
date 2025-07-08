import React from 'react';
import './Stats.css';

interface StatsProps {
  balance: number;
  totalInvested: number;
  activeInvestments: number;
  totalTransactions: number;
}

export const Stats: React.FC<StatsProps> = ({
  balance,
  totalInvested,
  activeInvestments,
  totalTransactions,
}) => {
  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount);
  };

  return (
    <div className="stats-container">
      <div className="stats-grid">
        <div className="stat-card">
          <div className="stat-icon balance-icon">💰</div>
          <div className="stat-content">
            <h4>Available Balance</h4>
            <p className="stat-value">{formatCurrency(balance)}</p>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon invested-icon">📈</div>
          <div className="stat-content">
            <h4>Total Invested</h4>
            <p className="stat-value">{formatCurrency(totalInvested)}</p>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon investments-icon">🎯</div>
          <div className="stat-content">
            <h4>Active Investments</h4>
            <p className="stat-value">{activeInvestments}</p>
          </div>
        </div>

        <div className="stat-card">
          <div className="stat-icon transactions-icon">📊</div>
          <div className="stat-content">
            <h4>Total Transactions</h4>
            <p className="stat-value">{totalTransactions}</p>
          </div>
        </div>
      </div>
    </div>
  );
}; 