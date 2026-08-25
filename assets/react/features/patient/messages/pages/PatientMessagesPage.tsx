import { usePatientMessages } from '../hooks/usePatientMessages';
import { ConversationList } from '../components/ConversationList';
import { MessageThread } from '../components/MessageThread';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/messages/_messages.scss';

export function PatientMessagesPage() {
    const { conversations, selectedConversation, selectConversation, isLoading, error } = usePatientMessages();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="messages-page">
            <div className="messages-page__header">
                <h1>Messages</h1>
                <p>Vos conversations</p>
            </div>
            <div className="messages-page__layout">
                <ConversationList
                    conversations={conversations}
                    selectedId={selectedConversation?.id}
                    onSelect={selectConversation}
                />
                {selectedConversation && <MessageThread thread={selectedConversation} />}
            </div>
        </div>
    );
}
