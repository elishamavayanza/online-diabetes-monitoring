import { Card } from '@/react/components/UI/Card';
import { useMessages } from '../hooks/useMessages';
import { ConversationList } from '../components/ConversationList';
import { MessageList } from '../components/MessageList';
import { MessageComposer } from '../components/MessageComposer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/clinician/messages/_messages.scss';

export function MessagesPage() {
    const {
        conversations,
        selectedConversation,
        selectConversation,
        sendMessage,
        isLoading,
        error,
        sendError,
    } = useMessages();
    const { pushAction } = useActionHistory();

    const handleSelectConversation = (id: string) => {
        const previousId = selectedConversation?.id ?? null;
        if (previousId && previousId !== id) {
            pushAction(() => selectConversation(previousId));
        }
        selectConversation(id);
    };

    if (isLoading) return <Spinner />;
    if (error && !selectedConversation) return <Alert variant="error">{error}</Alert>;

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
                        <MessageList thread={selectedConversation} />
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
