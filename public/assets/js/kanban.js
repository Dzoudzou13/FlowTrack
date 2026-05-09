document.addEventListener('DOMContentLoaded', function () {

  var dragged    = null;
  var draggedCol = null;

  // Nastavi drag-and-drop na vsetkych kartach.
  function initDragAndDrop() {
    document.querySelectorAll('.kanban-card').forEach(function (card) {
      card.setAttribute('draggable', 'true');

      card.addEventListener('dragstart', function (e) {
        dragged = card;
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
      });

      card.addEventListener('dragend', function () {
        card.classList.remove('dragging');
        dragged = null;
        document.querySelectorAll('.kanban-col-body').forEach(function (col) {
          col.classList.remove('drag-over');
        });
      });
    });

    document.querySelectorAll('.kanban-col-body').forEach(function (colBody) {
      colBody.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        colBody.classList.add('drag-over');
      });

      colBody.addEventListener('dragleave', function (e) {
        // Odstrán highlight len ked opustíme stĺpec (nie child element).
        if (!colBody.contains(e.relatedTarget)) {
          colBody.classList.remove('drag-over');
        }
      });

      colBody.addEventListener('drop', function (e) {
        e.preventDefault();
        colBody.classList.remove('drag-over');

        if (!dragged || dragged.closest('.kanban-col-body') === colBody) return;

        // Presun karty do nového stĺpca.
        var addBtn = colBody.querySelector('.kanban-add-btn');
        if (addBtn) {
          colBody.insertBefore(dragged, addBtn);
        } else {
          colBody.appendChild(dragged);
        }

        // Zisti nový status z data-status atributu stĺpca.
        var col = colBody.closest('.kanban-col');
        var newStatus = col ? col.getAttribute('data-status') : null;
        var taskId = dragged.getAttribute('data-task-id');

        updateCounts();

        if (taskId && newStatus) {
          sendStatusUpdate(taskId, newStatus);
        }
      });
    });
  }

  // Aktualizuj počítadlá v hlavičkách stĺpcov.
  function updateCounts() {
    document.querySelectorAll('.kanban-col').forEach(function (col) {
      var body = col.querySelector('.kanban-col-body');
      var count = body ? body.querySelectorAll('.kanban-card').length : 0;
      var countEl = col.querySelector('.kanban-col-count');
      if (countEl) countEl.textContent = count;
    });
  }

  // Pošle POST požiadavku na aktualizáciu statusu tasku.
  function sendStatusUpdate(taskId, newStatus) {
    fetch('/tasks/' + taskId + '/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'status=' + encodeURIComponent(newStatus),
    })
      .then(function (res) {
        if (!res.ok) {
          console.error('Status update failed:', res.status);
        }
      })
      .catch(function (err) {
        console.error('Status update error:', err);
      });
  }

  initDragAndDrop();
  initColumnDragAndDrop();

  // Drag-and-drop pre presúvanie stĺpcov.
  function initColumnDragAndDrop() {
    var layout = document.querySelector('.board-layout');
    if (!layout) return;

    document.querySelectorAll('.kanban-col:not(.kanban-col-add)').forEach(function (col) {
      var header = col.querySelector('.kanban-col-header');
      if (!header) return;

      header.style.cursor = 'grab';

      header.addEventListener('mousedown', function () {
        col.setAttribute('draggable', 'true');
      });
      col.addEventListener('dragend', function () {
        col.setAttribute('draggable', 'false');
      });

      col.addEventListener('dragstart', function (e) {
        if (dragged) return; // karty majú prioritu
        draggedCol = col;
        col.classList.add('col-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.stopPropagation();
      });

      col.addEventListener('dragend', function () {
        col.classList.remove('col-dragging');
        document.querySelectorAll('.kanban-col').forEach(function (c) {
          c.classList.remove('col-drag-over');
        });
        draggedCol = null;
        col.setAttribute('draggable', 'false');
        sendColumnOrder();
      });

      col.addEventListener('dragover', function (e) {
        if (!draggedCol || draggedCol === col || col.classList.contains('kanban-col-add')) return;
        e.preventDefault();
        e.stopPropagation();
        col.classList.add('col-drag-over');

        var rect      = col.getBoundingClientRect();
        var midX      = rect.left + rect.width / 2;
        var insertAfter = e.clientX > midX;
        var sibling   = insertAfter ? col.nextSibling : col;
        if (sibling !== draggedCol && sibling !== draggedCol.nextSibling) {
          layout.insertBefore(draggedCol, sibling);
        }
      });

      col.addEventListener('dragleave', function () {
        col.classList.remove('col-drag-over');
      });

      col.addEventListener('drop', function (e) {
        if (!draggedCol) return;
        e.preventDefault();
        e.stopPropagation();
        col.classList.remove('col-drag-over');
      });
    });
  }

  function sendColumnOrder() {
    var ids = [];
    document.querySelectorAll('.kanban-col:not(.kanban-col-add)').forEach(function (col) {
      var colId = col.getAttribute('data-col-id');
      if (colId) ids.push(colId);
    });
    if (!ids.length) return;
    fetch('/columns/reorder', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'ids=' + encodeURIComponent(JSON.stringify(ids)),
    }).catch(function (err) { console.error('Column reorder error:', err); });
  }
});
