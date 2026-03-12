import React, { useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Card } from '@/components/ui/Card';
import { Button } from '@/components/ui/Button';

type StatusType = 'idle' | 'success' | 'error';

const LEGACY_BASE = import.meta.env.DEV ? 'http://localhost/iasms' : '';

export function SubmitContractPage() {
  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [status, setStatus] = useState<StatusType>('idle');
  const [message, setMessage] = useState<string | null>(null);

  const handleChooseFile = () => {
    fileInputRef.current?.click();
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const selected = e.target.files?.[0] ?? null;
    setFile(selected);
    setStatus('idle');
    setMessage(selected ? null : message);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!file) {
      setStatus('error');
      setMessage('Please choose a contract PDF to upload.');
      return;
    }

    const formData = new FormData();
    formData.append('contract_file', file);
    formData.append('submit_contract', '1');

    setUploading(true);
    setStatus('idle');
    setMessage(null);

    try {
      const res = await fetch(`${LEGACY_BASE}/submit_contract.php`, {
        method: 'POST',
        body: formData,
        credentials: 'include',
      });

      const text = await res.text();

      if (!res.ok) {
        setStatus('error');
        setMessage('Upload failed. Please try again.');
        return;
      }

      let feedback = 'Contract submitted. You can refresh this page later to confirm status.';
      if (text.includes('Contract submitted successfully')) {
        feedback = 'Contract submitted successfully! Your contract is pending approval.';
      } else if (text.includes('Only PDF files are allowed')) {
        feedback = 'Only PDF files are allowed.';
      } else if (text.includes('File size must be less than 5MB')) {
        feedback = 'File size must be less than 5MB.';
      } else if (text.includes('Please select a contract file to upload')) {
        feedback = 'Please select a contract file to upload.';
      }

      setStatus(text.includes('successfully') ? 'success' : 'success');
      setMessage(feedback);
      setFile(null);
      if (fileInputRef.current) {
        fileInputRef.current.value = '';
      }
    } catch (err) {
      console.error(err);
      setStatus('error');
      setMessage('Unexpected error while uploading. Please check your connection and try again.');
    } finally {
      setUploading(false);
    }
  };

  const statusClasses =
    status === 'success'
      ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
      : status === 'error'
      ? 'bg-red-50 text-red-800 border-red-200'
      : 'bg-slate-50 text-slate-700 border-slate-200';

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <Link to="/student" className="text-sm text-primary-600 hover:underline">
          ← Back to Dashboard
        </Link>
      </div>

      <div className="rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 px-6 py-6 text-white shadow-lg">
        <h1 className="text-2xl font-display font-bold tracking-tight md:text-3xl">
          Submit Industrial Attachment Contract
        </h1>
        <p className="mt-2 text-primary-100 max-w-2xl text-sm md:text-base">
          Upload a signed copy of your industrial attachment contract. Once submitted, the contract will be
          reviewed by the administration and cannot be changed.
        </p>
      </div>

      {message && (
        <div className={`rounded-xl border px-4 py-3 text-sm ${statusClasses}`}>
          {message}
        </div>
      )}

      <form onSubmit={handleSubmit} className="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
        <Card padding="lg" className="flex flex-col justify-between bg-white">
          <div className="flex flex-1 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center hover:border-primary-300 hover:bg-primary-50/40">
            <div className="flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-600 shadow-sm">
              <span className="text-2xl">⬆</span>
            </div>
            <h2 className="mt-4 text-lg font-semibold text-slate-800">
              Select Contract PDF to Upload
            </h2>
            <p className="mt-2 text-sm text-slate-500">
              Supported format: <span className="font-medium">PDF</span> only. Maximum size{' '}
              <span className="font-medium">5MB</span>.
            </p>
            <div className="mt-6 flex flex-col items-center gap-3">
              <Button
                type="button"
                size="lg"
                onClick={handleChooseFile}
                disabled={uploading}
                className="px-6"
              >
                {file ? 'Change File' : 'Select File'}
              </Button>
              <input
                ref={fileInputRef}
                type="file"
                accept="application/pdf,.pdf"
                className="hidden"
                onChange={handleFileChange}
              />
              <p className="text-xs text-slate-500">
                {file ? (
                  <>
                    Selected file:{' '}
                    <span className="font-medium text-slate-700 break-all">{file.name}</span>
                  </>
                ) : (
                  'No file selected yet.'
                )}
              </p>
            </div>
          </div>
        </Card>

        <div className="space-y-4">
          <Card padding="lg" className="bg-white">
            <h2 className="text-base font-semibold text-slate-800">Contract Requirements</h2>
            <ul className="mt-3 space-y-2 text-sm text-slate-600">
              <li>• File must be in <span className="font-medium">PDF format</span>.</li>
              <li>• Maximum file size is <span className="font-medium">5MB</span>.</li>
              <li>• Contract must be signed by the student, company supervisor, and institution representative.</li>
              <li>• Ensure all required terms and conditions are included.</li>
              <li className="text-amber-700">
                • <span className="font-semibold">Important:</span> Once submitted, the contract cannot be changed.
              </li>
            </ul>
          </Card>

          <Card padding="lg" className="bg-white flex items-center justify-between gap-4">
            <div>
              <p className="text-sm font-medium text-slate-800">Ready to submit?</p>
              <p className="mt-1 text-xs text-slate-500">
                By submitting, you confirm that the contract is complete and correctly signed.
              </p>
            </div>
            <Button type="submit" size="lg" disabled={uploading} className="whitespace-nowrap">
              {uploading ? 'Submitting…' : 'Submit Contract'}
            </Button>
          </Card>
        </div>
      </form>
    </div>
  );
}

