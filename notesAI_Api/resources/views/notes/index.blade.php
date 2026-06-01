@extends('layouts.app')

@section('content')

<div class="container py-5">

    <div class="row mb-4">

        <div class="col-md-8">

            <h1 class="fw-bold">
                Notes AI Dashboard
            </h1>

            <p class="text-muted">
                AI Powered Notes Management System
            </p>

        </div>

        <div class="col-md-4 text-end">

          <button
    class="btn btn-primary"
    data-bs-toggle="modal"
    data-bs-target="#createModal">

    + Create Note

      </button>
      

        </div>
        

    </div>
    <div id="successBox" class="alert alert-success d-none"></div>

    <div class="card shadow">

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-6">

                  <input type="text" id="searchInput" onkeyup="searchNotes(this.value)" placeholder="Search notes...">

                </div>

            </div>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody id="notesTable">

                </tbody>

            </table>

           <div id="pagination" style="margin-top:10px;"></div>
        </div>

    </div>

</div>
<!-- Create Note Modal -->
<div class="modal fade"
     id="createModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header">

                <h5 class="modal-title fw-bold">
                    Create New Note
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div id="successBox"
                     class="alert alert-success d-none">
                </div>

                <div id="errorBox"
                     class="alert alert-danger d-none">
                </div>

                <form id="createNoteForm">

                    @csrf
                    <input type="hidden" id="note_id">
                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Note Title
                        </label>

                        <input type="text"
                               class="form-control"
                               id="title"
                               name="title"
                               placeholder="Enter note title">

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Note Content
                        </label>

                        <textarea
                            class="form-control"
                            rows="7"
                            id="content"
                            name="content"
                            placeholder="Write your note content here..."></textarea>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="saveNoteBtn">

                            Save Note

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
<!-- updatre modal -->
<div class="modal fade" id="editModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update Note</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_note_id">

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" id="edit_title" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Content</label>
                    <textarea id="edit_content" class="form-control"></textarea>
                </div>

                <div id="edit_msg"></div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="updateNote()">Update</button>
            </div>

        </div>

    </div>

</div>

<script>


document.addEventListener('DOMContentLoaded', function () {

    document
    .getElementById('createNoteForm')
    .addEventListener('submit', async function (e) {

        e.preventDefault();

        console.log('Form Submitted');

        let title = document.getElementById('title').value.trim();
        let content = document.getElementById('content').value.trim();

        let saveBtn = document.getElementById('saveNoteBtn');

        document
        .getElementById('successBox')
        .classList.add('d-none');

        document
        .getElementById('errorBox')
        .classList.add('d-none');

        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';

        try {

            const response = await axios.post(
                '/api/notes',
                {
                    title: title,
                    content: content
                },
                {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }
            );

            console.log(response.data);

            document
            .getElementById('successBox')
            .classList.remove('d-none');

            document
            .getElementById('successBox')
            .innerHTML =
                response.data.message ??
                'Note created successfully';

            document
            .getElementById('createNoteForm')
            .reset();

            setTimeout(() => {

                let modalElement =
                    document.getElementById('createModal');

                let modal =
                    bootstrap.Modal.getInstance(modalElement);

                if (modal) {
                    modal.hide();
                }

                location.reload();

            }, 1500);

        }
        catch (error) {

            console.log(error);

            let msg = 'Something went wrong';

            if (error.response) {

                if (error.response.status === 422) {

                    let errors =
                        error.response.data.errors;

                    msg = Object.values(errors)
                        .flat()
                        .join('<br>');

                }
                else {

                    msg =
                        error.response.data.message ??
                        'Server Error';
                }
            }

            document
            .getElementById('errorBox')
            .classList.remove('d-none');

            document
            .getElementById('errorBox')
            .innerHTML = msg;
        }
        finally {

            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Note';
        }

    });

});
let currentPage = 1;

document.addEventListener("DOMContentLoaded", function () {
    fetchNotes(currentPage);
});

function fetchNotes(page = 1) {

    axios.get(`/api/notes?page=${page}`)
        .then(response => {

            const res = response.data;
            const notes = res.data.list;

            currentPage = res.data.current_page;

            const tbody = document.getElementById("notesTable");
            tbody.innerHTML = "";

            notes.forEach((note, index) => {

                tbody.innerHTML += `
                    <tr id="row-${note.id}">
                        <td>${(res.data.current_page - 1) * res.data.per_page + index + 1}</td>
                        <td>${note.title ?? '-'}</td>
                        <td>${note.content ?? '-'}</td>
                        
                        <td class="d-flex">
                            <button onclick="editNote(${note.id})" class="btn btn-primary">Edit</button>
                            <button onclick="deleteNote(${note.id})" class="btn btn-danger ml-3" style="margin-left:7px;">Delete</button>
                            <button onclick="getSummary(${note.id})" class="btn btn-warning" style="margin-left:7px;">
    Summary
</button>
                        </td>
                    </tr>
                `;
            });

            renderPagination(res.data);

        })
        .catch(error => console.log(error));
}
function renderPagination(data) {

    let html = "";

    // Prev button
    html += `
        <button 
            onclick="changePage(${data.current_page - 1})" 
            ${data.current_page === 1 ? "disabled" : ""}>
            Prev
        </button>
    `;

    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <button 
                onclick="changePage(${i})"
                style="margin:2px; ${i === data.current_page ? 'background:black;color:white' : ''}">
                ${i}
            </button>
        `;
    }

    // Next button
    html += `
        <button 
            onclick="changePage(${data.current_page + 1})"
            ${data.current_page === data.last_page ? "disabled" : ""}>
            Next
        </button>
    `;

    document.getElementById("pagination").innerHTML = html;
}
function changePage(page) {
    fetchNotes(page);
}
function deleteNote(id) {

    if (!confirm("Are you sure you want to delete this note?")) {
        return;
    }

    axios.delete(`/api/notes/${id}`)
        .then(response => {

            console.log(response.data);

            // Row remove from DOM instantly
            document.getElementById(`row-${id}`).remove();

            alert("Note deleted successfully");

        })
        .catch(error => {
            console.error("Delete Error:", error);
            alert("Failed to delete note");
        });
}
function editNote(id) {

    axios.get(`/api/notes/${id}`)
        .then(res => {

            const note = res.data.data;

            document.getElementById("edit_note_id").value = note.id;
            document.getElementById("edit_title").value = note.title;
            document.getElementById("edit_content").value = note.content;

            const modal = new bootstrap.Modal(document.getElementById("editModal"));
            modal.show();

        })
        .catch(err => {
            console.error(err);
            alert("Failed to fetch data");
        });
}
function updateNote() {

    const id = document.getElementById("edit_note_id").value;

    const data = {
        title: document.getElementById("edit_title").value,
        content: document.getElementById("edit_content").value
    };

    axios.put(`/api/notes/${id}`, data)
        .then(res => {

            document.getElementById("edit_msg").innerHTML =
                `<div class="alert alert-success">Updated Successfully</div>`;

            setTimeout(() => {
                const modalEl = document.getElementById("editModal");
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                fetchNotes(currentPage); // refresh table

            }, 800);

        })
        .catch(err => {

            document.getElementById("edit_msg").innerHTML =
                `<div class="alert alert-danger">Update Failed</div>`;

            console.error(err);
        });
}
function getSummary(id) {

    axios.post(`/api/notes/${id}/summary`)
        .then(res => {

            alert("Summary: " + res.data.summary);

            console.log(res.data.summary);
        })
        .catch(err => {
            console.error(err);
        });
}

</script>

@endsection