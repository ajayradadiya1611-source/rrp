import React, { useState, useCallback } from 'react';
import InventoryList from './components/InventoryAdmin/InventoryList.jsx';
import InventoryForm from './components/InventoryAdmin/InventoryForm.jsx';

// view: { name: 'list' } | { name: 'add' } | { name: 'edit', id }
export default function App({ type }) {
  const [view, setView] = useState({ name: 'list' });

  const goList = useCallback(() => setView({ name: 'list' }), []);
  const goAdd = useCallback(() => setView({ name: 'add' }), []);
  const goEdit = useCallback((id) => setView({ name: 'edit', id }), []);

  return (
    <div className="ls-admin">
      {view.name === 'list' && (
        <InventoryList type={type} onAdd={goAdd} onEdit={goEdit} />
      )}
      {view.name === 'add' && (
        <InventoryForm type={type} mode="add" onDone={goList} onCancel={goList} />
      )}
      {view.name === 'edit' && (
        <InventoryForm type={type} mode="edit" id={view.id} onDone={goList} onCancel={goList} />
      )}
    </div>
  );
}
