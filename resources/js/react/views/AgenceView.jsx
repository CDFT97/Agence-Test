import React from "react";
import { createRoot } from "react-dom/client";
import AgenceForm from "../components/Agence";

const AgenceRootElement = document.getElementById("agence");

if (AgenceRootElement) {
  const root = createRoot(AgenceRootElement);

  // Obtener los datos pasados desde Blade
  const usersJson = AgenceRootElement.dataset.users;
  const initialFechaInicio = AgenceRootElement.dataset.fechaInicioCookie || '';
  const initialFechaFin = AgenceRootElement.dataset.fechaFinCookie || '';
  const csrfToken = AgenceRootElement.dataset.csrfToken || ''; 

  root.render(
    <AgenceForm
      initialUsersJson={usersJson}
      initialFechaInicio={initialFechaInicio}
      initialFechaFin={initialFechaFin}
      csrfToken={csrfToken}
    />
  );
}