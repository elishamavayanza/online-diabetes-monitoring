import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { ConversationList } from '@/react/features/clinician/messages/components/ConversationList';
import { MessageList } from '@/react/features/clinician/messages/components/MessageList';
import { MessageComposer } from '@/react/features/clinician/messages/components/MessageComposer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useIsCompact } from '@/react/hooks/useIsCompact';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { usePatientMessages } from '../hooks/usePatientMessages';
import '@/styles/pages/clinician/messages/_messages.scss';
import '@/styles/pages/patient/messages/_messages.scss';

export function PatientMessagesPage() {
    const {
        conversations,
        selectedConversation,
        selectConversation,
        sendMessage,
        deleteMessage,
        isLoading,
        error,
        sendError,
    } = usePatientMessages();

    const isCompact = useIsCompact();
    const { undoLastAction } = useActionHistory();
    const [mobileView, setMobileView] = useState<'list' | 'thread'>('list');

    const selectedId = selectedConversation?.id ?? '';

    const handleSelect = (id: string) => {
        selectConversation(id);
        if (isCompact) {
            setMobileView('thread');
        }
    };

    const handleExitMessages = () => {
        const undone = undoLastAction();
        if (!undone) {
            window.history.back();
        }
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    if (isCompact) {
        return (
            <div className="patient-messages-page">
                <div className="messages-page messages-page--mobile">
                    {mobileView === 'list' ? (
                        <ConversationList
                            conversations={conversations}
                            selectedId={selectedId}
                            onSelect={handleSelect}
                            onBack={handleExitMessages}
                        />
                    ) : (
                        selectedConversation && (
                            <Card className="message-thread message-thread--mobile">
                                <MessageList
                                    thread={selectedConversation}
                                    onDeleteMessage={deleteMessage}
                                    onBack={() => setMobileView('list')}
                                />
                                <MessageComposer
                                    onSendMessage={(content, media) =>
                                        sendMessage(selectedConversation.id, content, media)
                                    }
                                />
                            </Card>
                        )
                    )}
                </div>
            </div>
        );
    }

    return (
        <div className="patient-messages-page">
            <div className="messages-page">
                <div className="messages-page__header">
                    <h1>Messages</h1>
                    <p>Vos conversations</p>
                    {sendError && <Alert variant="error">{sendError}</Alert>}
                </div>

                {selectedConversation && (
                    <div className="messages-page__layout">
                        <div className="message-thread">
                            <MessageList
                                thread={selectedConversation}
                                onDeleteMessage={deleteMessage}
                            />
                            <MessageComposer
                                onSendMessage={(content, media) =>
                                    sendMessage(selectedConversation.id, content, media)
                                }
                            />
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}