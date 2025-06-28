import React from 'react';
import './QuickActions.css';

export const QuickActions: React.FC = () => {
  const actions = [
    {
      id: 'invest',
      title: 'Make Investment',
      description: 'Start a new investment',
      icon: '📈',
      url: '/invest',
      color: 'primary',
    },
    {
      id: 'deposit',
      title: 'Deposit Funds',
      description: 'Add money to your account',
      icon: '💳',
      url: '/deposit',
      color: 'success',
    },
    {
      id: 'withdraw',
      title: 'Withdraw Funds',
      description: 'Withdraw your earnings',
      icon: '💸',
      url: '/withdraw',
      color: 'warning',
    },
    {
      id: 'profile',
      title: 'Update Profile',
      description: 'Manage your account',
      icon: '👤',
      url: '/profile',
      color: 'info',
    },
  ];

  const handleActionClick = (url: string) => {
    window.location.href = url;
  };

  return (
    <div className="quick-actions-container">
      <h3>Quick Actions</h3>
      <div className="actions-grid">
        {actions.map(action => (
          <div
            key={action.id}
            className={`action-card ${action.color}`}
            onClick={() => handleActionClick(action.url)}
          >
            <div className="action-icon">{action.icon}</div>
            <div className="action-content">
              <h4>{action.title}</h4>
              <p>{action.description}</p>
            </div>
            <div className="action-arrow">→</div>
          </div>
        ))}
      </div>
    </div>
  );
}; 