import React, { useState, useEffect } from 'react';

interface ConnectionStatus {
  wordpress: boolean;
  react: boolean;
  database: boolean;
  cors: boolean;
}

interface ConnectionMonitorProps {
  settings?: any;
}

const ConnectionMonitor: React.FC<ConnectionMonitorProps> = ({ settings }) => {
  const [status, setStatus] = useState<ConnectionStatus>({
    wordpress: false,
    react: false,
    database: false,
    cors: false
  });
  
  const [loading, setLoading] = useState(true);
  const [lastCheck, setLastCheck] = useState<Date | null>(null);

  useEffect(() => {
    checkConnections();
    const interval = setInterval(checkConnections, 5000); // Check every 5 seconds
    
    return () => clearInterval(interval);
  }, []);

  const checkConnections = async () => {
    const newStatus: ConnectionStatus = {
      wordpress: false,
      react: false,
      database: false,
      cors: false
    };

    try {
      // Check WordPress connection
      const wpResponse = await fetch('/wp-json/');
      newStatus.wordpress = wpResponse.ok;

      // Check React dev server
      try {
        const reactResponse = await fetch('http://localhost:5174', { 
          mode: 'no-cors',
          cache: 'no-cache'
        });
        newStatus.react = true;
      } catch {
        newStatus.react = false;
      }

      // Check database via WordPress API
      try {
        const dbResponse = await fetch('/wp-json/wp/v2/posts?per_page=1');
        newStatus.database = dbResponse.ok;
      } catch {
        newStatus.database = false;
      }

      // Check CORS headers
      try {
        const corsResponse = await fetch('/wp-json/blackcnote/v1/health');
        const corsHeaders = corsResponse.headers.get('Access-Control-Allow-Origin');
        newStatus.cors = !!corsHeaders;
      } catch {
        newStatus.cors = false;
      }

    } catch (error) {
      console.error('Connection check failed:', error);
    }

    setStatus(newStatus);
    setLoading(false);
    setLastCheck(new Date());
  };

  const getStatusIcon = (isConnected: boolean) => {
    return isConnected ? '✅' : '❌';
  };

  const getStatusText = (isConnected: boolean) => {
    return isConnected ? 'Connected' : 'Disconnected';
  };

  const getStatusColor = (isConnected: boolean) => {
    return isConnected ? 'text-green-500' : 'text-red-500';
  };

  if (loading) {
    return (
      <div className="connection-monitor loading">
        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
        <span className="ml-2">Checking connections...</span>
      </div>
    );
  }

  return (
    <div className="connection-monitor bg-white rounded-lg shadow-md p-4 mb-4">
      <h3 className="text-lg font-semibold mb-3 flex items-center">
        🔗 Connection Status
        <button 
          onClick={checkConnections}
          className="ml-auto text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors"
        >
          Refresh
        </button>
      </h3>
      
      <div className="grid grid-cols-2 gap-3">
        <div className="flex items-center space-x-2">
          <span>{getStatusIcon(status.wordpress)}</span>
          <span className="font-medium">WordPress</span>
          <span className={`text-sm ${getStatusColor(status.wordpress)}`}>
            {getStatusText(status.wordpress)}
          </span>
        </div>
        
        <div className="flex items-center space-x-2">
          <span>{getStatusIcon(status.react)}</span>
          <span className="font-medium">React Dev</span>
          <span className={`text-sm ${getStatusColor(status.react)}`}>
            {getStatusText(status.react)}
          </span>
        </div>
        
        <div className="flex items-center space-x-2">
          <span>{getStatusIcon(status.database)}</span>
          <span className="font-medium">Database</span>
          <span className={`text-sm ${getStatusColor(status.database)}`}>
            {getStatusText(status.database)}
          </span>
        </div>
        
        <div className="flex items-center space-x-2">
          <span>{getStatusIcon(status.cors)}</span>
          <span className="font-medium">CORS</span>
          <span className={`text-sm ${getStatusColor(status.cors)}`}>
            {getStatusText(status.cors)}
          </span>
        </div>
      </div>
      
      {lastCheck && (
        <div className="mt-3 text-xs text-gray-500">
          Last checked: {lastCheck.toLocaleTimeString()}
        </div>
      )}
      
      <div className="mt-3 p-2 bg-gray-50 rounded text-xs">
        <div className="font-medium mb-1">Development Info:</div>
        <div>WordPress URL: {settings?.homeUrl || 'Not configured'}</div>
        <div>React URL: {settings?.devServerUrl || 'http://localhost:5174'}</div>
        <div>Environment: {settings?.environment || 'unknown'}</div>
      </div>
    </div>
  );
};

export default ConnectionMonitor; 