import React from 'react';
import { Modal } from '@/react/components/UI/Modal';
import { Badge } from '@/react/components/UI/Badge';
import { Notification, NotificationType } from '../types';

interface NotificationDetailsModalProps {
    notification: Notification | null;
    isOpen: boolean;
    onClose: () => void;
}

const typeVariant: Partial<Record<NotificationType, 'success' | 'warning' | 'error'>> = {
    SYSTEM_ALERT: 'error',
    MESSAGE_RECEIVED: 'success',
    PRESCRIPTION_UPDATED: 'warning',
};

export function NotificationDetailsModal({ notification, isOpen, onClose }: NotificationDetailsModalProps) {
    if (!notification) return null;

    return (
        <Modal isOpen={isOpen} onClose={onClose} size="small">
            <div className="notification-details">
                <h2>{notification.titre}</h2>
                <p>{notification.message}</p>
                <div className="notification-details__meta">
                    <Badge variant={typeVariant[notification.type] ?? 'secondary'}>
                        {notification.type}
                    </Badge>
                    <span className="notification-details__date">{notification.date}</span>
                </div>
            </div>
        </Modal>
    );
}
