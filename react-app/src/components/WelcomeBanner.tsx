import React from 'react';

interface WelcomeBannerProps {
  title?: string;
  subtitle?: string;
  showAnimation?: boolean;
}

const WelcomeBanner: React.FC<WelcomeBannerProps> = ({ 
  title = "Welcome to BlackCnote", 
  subtitle = "Your premium investment platform",
  showAnimation = true 
}) => {
  return (
    <div className={`welcome-banner ${showAnimation ? 'animate-fade-in' : ''}`}>
      <div className="banner-content">
        <h1 className="banner-title">{title}</h1>
        <p className="banner-subtitle">{subtitle}</p>
        <div className="banner-features">
          <div className="feature">
            <span className="feature-icon">🚀</span>
            <span>Fast Development</span>
          </div>
          <div className="feature">
            <span className="feature-icon">⚡</span>
            <span>Hot Reload</span>
          </div>
          <div className="feature">
            <span className="feature-icon">🎨</span>
            <span>Modern UI</span>
          </div>
        </div>
      </div>
      
      <style jsx>{`
        .welcome-banner {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          padding: 3rem 2rem;
          text-align: center;
          border-radius: 12px;
          margin: 2rem 0;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .banner-content {
          max-width: 800px;
          margin: 0 auto;
        }
        
        .banner-title {
          font-size: 2.5rem;
          font-weight: bold;
          margin-bottom: 1rem;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .banner-subtitle {
          font-size: 1.2rem;
          margin-bottom: 2rem;
          opacity: 0.9;
        }
        
        .banner-features {
          display: flex;
          justify-content: center;
          gap: 2rem;
          flex-wrap: wrap;
        }
        
        .feature {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          background: rgba(255, 255, 255, 0.1);
          padding: 0.75rem 1.5rem;
          border-radius: 25px;
          backdrop-filter: blur(10px);
        }
        
        .feature-icon {
          font-size: 1.2rem;
        }
        
        .animate-fade-in {
          animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        @media (max-width: 768px) {
          .banner-title {
            font-size: 2rem;
          }
          
          .banner-features {
            flex-direction: column;
            align-items: center;
          }
        }
      `}</style>
    </div>
  );
};

export default WelcomeBanner; 