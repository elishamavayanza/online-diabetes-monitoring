import { useState } from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
import { Card } from '@/react/components/UI/Card';
import { useMessages } from '../hooks/useMessages';
import { ConversationList } from '@/react/features/clinician/messages/components/ConversationList';
import { MessageList } from '@/react/features/clinician/messages/components/MessageList';
import { MessageComposer } from '@/react/features/clinician/messages/components/MessageComposer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useSearchParams } from 'react-router-dom';
import { useIsCompact } from '@/react/hooks/useIsCompact';
import '@/styles/pages/clinician/messages/_messages.scss';

export function MessagesPages() {
    const { t } = useI18n();
    const [searchParams] = useSearchParams();
    const initialConversationId = searchParams.get('conversationId') || undefined;
    const isCompact = useIsCompact(); // mobile, tablette ou portrait → rafraîchissement de la vue

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

    const { pushAction, undoLastAction } = useActionHistory();
    const [mobileView, setMobileView] = useState<'list' | 'thread'>(
        initialConversationId ? 'thread' : 'list'
    );

    const handleSelectConversation = (id: string) => {
        const previousId = selectedConversation?.id ?? null;
        if (previousId && previousId !== id) {
            pushAction(() => selectConversation(previousId));
        }
        void selectConversation(id);
        if (isCompact) {
            setMobileView('thread');
        }
    };

    const handleBackToList = () => {
        setMobileView('list');
    };

    const handleExitMessages = () => {
        const undone = undoLastAction();
        if (!undone) {
            window.history.back();
        }
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error && !selectedConversation) {
        return <Alert variant="error">{error}</Alert>;
    }

    if (isCompact) {
        return (
            <div className="messages-page messages-page--mobile">
                {mobileView === 'list' ? (
                    <ConversationList
                        conversations={conversations}
                        selectedId={selectedConversation?.id}
                        onSelect={handleSelectConversation}
                        onBack={handleExitMessages}
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
                <h1>{t('Messages')}</h1>
                <p>{t('Vos conversations')}</p>
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
