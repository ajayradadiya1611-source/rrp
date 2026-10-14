import React, { useState } from 'react';

const QUESTIONS = [
  {
    id: 'contact_by_phone',
    title: 'Can we contact you by phone if any issues arise with your order?',
    type: 'icon-choice',
    options: [
      { value: 'yes', label: 'Yes', icon: 'fa-solid fa-phone' },
      { value: 'no', label: 'No', icon: 'fa-solid fa-phone-slash' },
    ],
  },
  {
    id: 'heard_about_us',
    title: 'How did you first hear about Loose Grown Diamond?',
    type: 'list-choice',
    allowOther: true,
    options: [
      'Google',
      'Reddit',
      'AI (ChatGPT, Gemini, Claude, Grok, etc.)',
      'Friend, Family Member, or Partner',
      'TikTok',
      'Facebook or Instagram',
      'YouTube',
      'Trustpilot',
      'Other',
    ],
  },
  {
    id: 'when_heard_about_us',
    title: 'When did you first hear about us?',
    type: 'list-choice',
    options: ['About a week', '1–3 months', '3–6 months', '6+ months', 'Not sure'],
  },
  {
    id: 'almost_kept_from_ordering',
    title: 'What almost kept you from ordering today?',
    type: 'text',
    optional: true,
  },
  {
    id: 'compared_others',
    title: 'Did you compare us with other jewelers or online retailers?',
    type: 'yes-no-text',
    yesLabel: 'Yes',
    noLabel: 'No',
    yesTextPlaceholder: 'Which ones? (optional)',
  },
  {
    id: 'could_do_better',
    title: 'Is there anything we could have done better?',
    type: 'text',
    optional: true,
  },
];

export default function App() {
  const {
    orderId = 0,
    apiBaseUrl = '',
    nonce = '',
    alreadyAnswered = false,
  } = (typeof window !== 'undefined' && window.LS_THANKYOU_QUESTION) || {};

  const [step, setStep] = useState(0);
  const [answers, setAnswers] = useState({});
  const [otherText, setOtherText] = useState('');
  const [compareText, setCompareText] = useState('');
  const [status, setStatus] = useState(alreadyAnswered ? 'submitted' : 'idle'); // idle | submitting | submitted | error
  const [errorMsg, setErrorMsg] = useState('');

  if (!orderId) {
    return null;
  }

  if (status === 'submitted') {
    return (
      <div className="ls-tq">
        <div className="ls-tq__card ls-tq__card--done">
          <div className="ls-tq__icon-circle ls-tq__icon-circle--done">
            <i className="fa-solid fa-check" />
          </div>
          <h3 className="ls-tq__done-title">Thanks! We appreciate your feedback.</h3>
        </div>
      </div>
    );
  }

  const question = QUESTIONS[step];
  const total = QUESTIONS.length;
  const isLast = step === total - 1;

  const setAnswer = (value) => {
    setAnswers((prev) => ({ ...prev, [question.id]: value }));
    setErrorMsg('');
  };

  const currentAnswer = answers[question.id];

  const canContinue = () => {
    if (question.optional) return true;
    if (question.type === 'text') return true;
    if (question.type === 'icon-choice') return !!currentAnswer;
    if (question.type === 'yes-no-text') return !!currentAnswer;
    if (question.type === 'list-choice') {
      if (!currentAnswer) return false;
      if (currentAnswer === 'Other' && question.allowOther) return otherText.trim() !== '';
      return true;
    }
    return true;
  };

  // The Continue button is only needed when there's no single click that can
  // advance the step on its own (free-text steps, or a follow-up text field
  // after choosing "Other" / "Yes"). Simple radio picks auto-advance instead.
  const showContinueButton = (() => {
    if (question.type === 'text') return true;
    if (question.type === 'icon-choice') return false;
    if (question.type === 'list-choice') return question.allowOther && currentAnswer === 'Other';
    if (question.type === 'yes-no-text') return currentAnswer === 'yes';
    return true;
  })();

  const buildPayload = (answersObj = answers) => {
    const payload = { ...answersObj };
    if (answersObj.heard_about_us === 'Other') {
      payload.heard_about_us = otherText.trim() ? `Other: ${otherText.trim()}` : 'Other';
    }
    if (answersObj.compared_others === 'yes' && compareText.trim()) {
      payload.compared_others = `Yes (${compareText.trim()})`;
    }
    return payload;
  };

  const handleSubmit = async (answersObj = answers) => {
    setStatus('submitting');
    setErrorMsg('');

    try {
      const response = await fetch(`${apiBaseUrl}/thankyou-question`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify({
          order_id: orderId,
          answer: JSON.stringify(buildPayload(answersObj)),
        }),
      });

      const data = await response.json();

      if (!response.ok || data.success === false) {
        throw new Error(data.message || 'Something went wrong. Please try again.');
      }

      setStatus('submitted');
    } catch (err) {
      setStatus('error');
      setErrorMsg(err.message || 'Something went wrong. Please try again.');
    }
  };

  const handleContinue = () => {
    if (!canContinue()) {
      setErrorMsg('Please answer to continue.');
      return;
    }
    if (isLast) {
      handleSubmit();
    } else {
      setStep((s) => s + 1);
    }
  };

  // For simple radio-style questions, selecting an option advances automatically
  // (no need to also click Continue). Questions that need a follow-up text field
  // ("Other", "Yes — which ones?") wait for the Continue click instead.
  const selectAndAdvance = (value) => {
    const updatedAnswers = { ...answers, [question.id]: value };
    setAnswers(updatedAnswers);
    setErrorMsg('');

    setTimeout(() => {
      if (isLast) {
        handleSubmit(updatedAnswers);
      } else {
        setStep((s) => s + 1);
      }
    }, 200);
  };

  return (
    <div className="ls-tq">
      <div className="ls-tq__card">
        <h2 className="ls-tq__title">One quick question before you go</h2>
        <p className="ls-tq__subtitle">Your feedback helps us improve and serve you better.</p>

        <div className="ls-tq__progress">
          {QUESTIONS.map((q, i) => (
            <span
              key={q.id}
              className={`ls-tq__progress-segment${i <= step ? ' is-filled' : ''}`}
            />
          ))}
        </div>
        <p className="ls-tq__step-label">Question {step + 1} of {total}</p>

        <h3 className="ls-tq__question">{question.title}</h3>

        {question.type === 'icon-choice' && (
          <div className="ls-tq__icon-options">
            {question.options.map((opt) => {
              const selected = currentAnswer === opt.value;
              return (
                <button
                  type="button"
                  key={opt.value}
                  className={`ls-tq__icon-option${selected ? ' is-selected' : ''}`}
                  onClick={() => selectAndAdvance(opt.value)}
                >
                  <span className={`ls-tq__icon-bubble${selected ? ' is-selected' : ''}`}>
                    <i className={opt.icon} />
                  </span>
                  <span className="ls-tq__icon-option-label">{opt.label}</span>
                </button>
              );
            })}
          </div>
        )}

        {question.type === 'list-choice' && (
          <div className="ls-tq__list-options">
            {question.options.map((opt) => {
              const selected = currentAnswer === opt;
              return (
                <label key={opt} className={`ls-tq__list-option${selected ? ' is-selected' : ''}`}>
                  <input
                    type="radio"
                    name={question.id}
                    checked={selected}
                    onChange={() => {
                      if (question.allowOther && opt === 'Other') {
                        setAnswer(opt);
                      } else {
                        selectAndAdvance(opt);
                      }
                    }}
                  />
                  <span className="ls-tq__list-option-dot" />
                  <span>{opt}</span>
                </label>
              );
            })}
            {question.allowOther && currentAnswer === 'Other' && (
              <input
                type="text"
                className="ls-tq__text-input ls-tq__text-input--inline"
                placeholder="Tell us more"
                value={otherText}
                onChange={(e) => setOtherText(e.target.value)}
              />
            )}
          </div>
        )}

        {question.type === 'text' && (
          <textarea
            className="ls-tq__textarea"
            placeholder="Type your answer here..."
            value={currentAnswer || ''}
            onChange={(e) => setAnswer(e.target.value)}
            rows={3}
          />
        )}

        {question.type === 'yes-no-text' && (
          <div className="ls-tq__icon-options ls-tq__icon-options--text-only">
            <button
              type="button"
              className={`ls-tq__text-option${currentAnswer === 'yes' ? ' is-selected' : ''}`}
              onClick={() => setAnswer('yes')}
            >
              {question.yesLabel}
            </button>
            <button
              type="button"
              className={`ls-tq__text-option${currentAnswer === 'no' ? ' is-selected' : ''}`}
              onClick={() => selectAndAdvance('no')}
            >
              {question.noLabel}
            </button>
          </div>
        )}
        {question.type === 'yes-no-text' && currentAnswer === 'yes' && (
          <input
            type="text"
            className="ls-tq__text-input"
            placeholder={question.yesTextPlaceholder}
            value={compareText}
            onChange={(e) => setCompareText(e.target.value)}
          />
        )}

        {errorMsg && <p className="ls-tq__error">{errorMsg}</p>}

        <p className="ls-tq__secure">
          <i className="fa-solid fa-lock" /> Your information is secure and will only be used for this order.
        </p>

        {showContinueButton && (
          <button
            type="button"
            className="ls-tq__continue"
            onClick={handleContinue}
            disabled={status === 'submitting'}
          >
            {status === 'submitting' ? 'Submitting...' : isLast ? 'Submit' : 'Continue'}
            {status !== 'submitting' && <i className="fa-solid fa-arrow-right" />}
          </button>
        )}
      </div>
    </div>
  );
}
