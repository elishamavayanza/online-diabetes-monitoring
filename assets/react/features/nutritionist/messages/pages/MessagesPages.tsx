import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { useMessages } from '../hooks/useMessages';
import { ConversationList } from '@/react/features/clinician/messages/components/ConversationList';
import { MessageList } from '@/react/features/clinician/messages/components/MessageList';
import { MessageComposer } from '@/react/features/clinician/messages/components/MessageComposer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useSearchParams } from 'react-router-dom';
import { useIsMobile } from '@/react/hooks/useIsMobile';
import '@/styles/pages/clinician/messages/_messages.scss';

export function MessagesPages() {
    const [searchParams] = useSearchParams();
    const initialConversationId = searchParams.get('conversationId') || undefined;
    const isMobile = useIsMobile();

    const {
        conversations,
        selectedConversation,
        selectConversation,
        sendMessage,
        deleteMessage,
        isLoading,
        error,
        sendError,
    } = useMessages(initialConversationId);

    const { pushAction } = useActionHistory();
    const [mobileView, setMobileView] = useState<'list' | 'thread'>(
        initialConversationId ? 'thread' : 'list'
    );

    const handleSelectConversation = (id: string) => {
        const previousId = selectedConversation?.id ?? null;
        if (previousId && previousId !== id) {
            pushAction(() => selectConversation(previousId));
        }
        void selectConversation(id);
        if (isMobile) {
            setMobileView('thread');
        }
    };

    const handleBackToList = () => {
        setMobileView('list');
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error && !selectedConversation) {
        return <Alert variant="error">{error}</Alert>;
    }

    if (isMobile) {
        return (
            <div className="messages-page messages-page--mobile">
                {mobileView === 'list' ? (
                    <ConversationList
                        conversations={conversations}
                        selectedId={selectedConversation?.id}
                        onSelect={handleSelectConversation}
                    />
                ) : (
                    selectedConversation && (
                        <Card className="message-thread message-thread--mobile">
                            <MessageList
                                thread={selectedConversation}
                                onDeleteMessage={deleteMessage}
                                onBack={handleBackToList}
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
        );
    }

    return (
        <div className="messages-page">
            <div className="messages-page__header">
                <h1>Messages</h1>
                <p>Vos conversations</p>
                {sendError && <Alert variant="error">{sendError}</Alert>}
            </div>
            <div className="messages-page__layout">
                <ConversationList
                    conversations={conversations}
                    selectedId={selectedConversation?.id}
                    onSelect={handleSelectConversation}
                />
                {selectedConversation && (
                    <Card className="message-thread">
                        <MessageList
                            thread={selectedConversation}
                            onDeleteMessage={deleteMessage}
                        />
                        <MessageComposer
                            onSendMessage={(content, media) =>
                                sendMessage(selectedConversation.id, content, media)
                            }
                        />
                    </Card>
                )}
            </div>
        </div>
    );
}
