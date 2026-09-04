import { useState } from 'react';
import { Select } from '@/react/components/Forms/Select';
import { MessageList } from '@/react/features/clinician/messages/components/MessageList';
import { MessageComposer } from '@/react/features/clinician/messages/components/MessageComposer';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { usePatientMessages } from '../hooks/usePatientMessages';
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

    const [activeId, setActiveId] = useState<string>('');

    const selectedId = selectedConversation?.id ?? '';

    const handleSelect = (id: string) => {
        setActiveId(id);
        selectConversation(id);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const conversationOptions = conversations.map((c) => ({
        value: c.id,
        label: c.participant,
    }));

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
