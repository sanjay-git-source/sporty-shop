<style>

body {
        background-color: #f4f4f9;
    }
    .search_section {
        position: sticky;
        top: 0;
        background-color: #f4f4f9;
        padding: 20px 0;
        text-align: center;
        margin-top: 0;
    }
    .search_section h3 {
        color: #F76100;
        font-weight: 600;
    }
    .search_section input[type="text"] {
    padding: 10px;
    border: 1px solid #F76100;
    border-radius: 5px;
    width: 300px; /* Set a fixed width */
    max-width: 100%; /* Ensure it doesn't overflow */
    transition: border-color 0.3s ease;
}

.search_section input[type="text"]:focus {
    outline: none;
    border: 1px solid #FF7F50  ;

}

.search_section input[type="text"]::placeholder {
    color: #999;
}

    .btn {
        background-color: #FF7F50  ;
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 5px;
        cursor: pointer;        
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .btn:hover {
        background-color: #F76100;
        color: white;
    }
    .wholetbl {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: auto;
        padding: 10px;
    }
    .container {
        background-color: white;
        width: 100%;
        max-width: 1500px; /* Set max width for the container */
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 70vh; /* Limit the container's height */
        position: relative;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .header h2 {
        margin: 0;
        color: #F76100;
        font-size: 20px;
    }
    .header .buttons {
        display: flex;
        gap: 10px;
    }
    .header .buttons button {
        background-color: #FF7F50    ;
        border: none;
        color: white;
        padding: 12px 16px;
        border-radius: 5px;
        cursor: pointer;        
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .header .buttons button:hover {
        background-color: #F76100;
    }
    .table-wrapper {
        overflow-x: auto;
        flex-grow: 1;
        margin-top: 20px;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 16px;
        border-radius: 5px;
        overflow: hidden;
    }
    thead {
        background-color: #F76100;
        color: white;
    }
    thead th {
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }
    tbody tr:nth-child(even) {
        background-color: #f4f4f9;
    }
    tbody td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }
    @media (max-width: 768px) {
        h1 {
            font-size: 26px;
            text-align: center;
        }
        .header h2 {
            font-size: 18px;
        }
        .header .buttons button {
            padding: 10px 14px;
            font-size: 14px;
        }
        table {
            font-size: 14px;
        }
        thead th, tbody td {
            padding: 10px;
        }
    }
    @media (max-width: 480px) {
        body {
            padding: 0px;
        }
        h1 {
            font-size: 1.5rem;
        }
        .header h2 {
            font-size: 16px;
        }
        .header .buttons button {
            padding: 6px 12px;
            font-size: 12px;
        }
        table {
            display: block;
            width: 100%;
            overflow-x: auto;
            font-size: 12px;
        }
        thead {
            display: none;
        }
        tbody {
            display: block;
        }
        tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
        tbody td {
            display: block;
            text-align: right;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            position: relative;
            padding-left: 50%;
        }
        tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            top: 10px;
            font-weight: bold;
            color: #333;
        }
    }
    </style>