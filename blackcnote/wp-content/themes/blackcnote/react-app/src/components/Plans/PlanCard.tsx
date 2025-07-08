import React, { useState } from 'react';
import { Plan } from '../../api/hyiplab';
import './PlanCard.css';

interface PlanCardProps {
  plan: Plan;
}

export const PlanCard: React.FC<PlanCardProps> = ({ plan }) => {
  const [isExpanded, setIsExpanded] = useState(false);

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount);
  };

  const formatPercentage = (rate: number) => {
    return `${rate}%`;
  };

  const getStatusBadge = (status: number) => {
    return status === 1 ? (
      <span className="status-badge active">Active</span>
    ) : (
      <span className="status-badge inactive">Inactive</span>
    );
  };

  return (
    <div className={`plan-card ${isExpanded ? 'expanded' : ''}`}>
      <div className="plan-header">
        <h3 className="plan-name">{plan.name}</h3>
        {getStatusBadge(plan.status)}
      </div>
      
      <div className="plan-description">
        <p>{plan.description}</p>
      </div>

      <div className="plan-details">
        <div className="detail-row">
          <span className="detail-label">Interest Rate:</span>
          <span className="detail-value highlight">{formatPercentage(plan.interest_rate)}</span>
        </div>
        
        <div className="detail-row">
          <span className="detail-label">Term:</span>
          <span className="detail-value">{plan.term_days} days</span>
        </div>
        
        <div className="detail-row">
          <span className="detail-label">Min Investment:</span>
          <span className="detail-value">{formatCurrency(plan.min_amount)}</span>
        </div>
        
        <div className="detail-row">
          <span className="detail-label">Max Investment:</span>
          <span className="detail-value">{formatCurrency(plan.max_amount)}</span>
        </div>
      </div>

      {isExpanded && (
        <div className="plan-extra">
          <div className="detail-row">
            <span className="detail-label">Created:</span>
            <span className="detail-value">
              {new Date(plan.created_at).toLocaleDateString()}
            </span>
          </div>
          <div className="detail-row">
            <span className="detail-label">Last Updated:</span>
            <span className="detail-value">
              {new Date(plan.updated_at).toLocaleDateString()}
            </span>
          </div>
        </div>
      )}

      <div className="plan-actions">
        <button 
          className="btn-expand"
          onClick={() => setIsExpanded(!isExpanded)}
        >
          {isExpanded ? 'Show Less' : 'Show More'}
        </button>
        
        {plan.status === 1 && (
          <button 
            className="btn-invest"
            onClick={() => {
              // Navigate to invest page with plan pre-selected
              window.location.href = `/invest?plan=${plan.id}`;
            }}
          >
            Invest Now
          </button>
        )}
      </div>
    </div>
  );
};

export default PlanCard; 