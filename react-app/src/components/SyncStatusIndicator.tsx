import React from 'react';
import { CheckCircle, RefreshCw, AlertCircle, WifiOff } from 'lucide-react';
import { useLiveEditing } from '../hooks/useLiveEditing';

const statusMap = {
  connected: {
    icon: <CheckCircle className="h-5 w-5 text-green-500" />, label: 'Connected'
  },
  syncing: {
    icon: <RefreshCw className="h-5 w-5 text-yellow-500 animate-spin" />, label: 'Syncing...'
  },
  error: {
    icon: <AlertCircle className="h-5 w-5 text-red-500" />, label: 'Sync Error'
  },
  offline: {
    icon: <WifiOff className="h-5 w-5 text-gray-400" />, label: 'Offline'
  }
};

export const SyncStatusIndicator: React.FC = () => {
  const [liveEditingState] = useLiveEditing();
  let status: keyof typeof statusMap = 'offline';
  if (liveEditingState.connected) {
    if (liveEditingState.errors.length > 0) {
      status = 'error';
    } else if (liveEditingState.pendingChanges > 0) {
      status = 'syncing';
    } else {
      status = 'connected';
    }
  }
  const { icon, label } = statusMap[status];
  return (
    <div className="flex items-center space-x-2 group cursor-pointer" title={label}>
      {icon}
      <span className="text-xs text-gray-600 group-hover:inline hidden">{label}</span>
    </div>
  );
}; 