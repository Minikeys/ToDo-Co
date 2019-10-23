INSERT INTO `user` (`id`, `username`, `password`, `email`, `roles`) VALUES
(1, 'admin', '$2y$13$yoBRdv2upMMYonig5VuWKuZMufw7SzeoW3mfIMEfhjpQ0spMz2A5u', 'admin@todoco.com', '[\"ROLE_ADMIN\"]'),
(2, 'user', '$2y$13$U.ujX44qCQLmkueX1KMiPO/1RuFYduUh2QZ7eLgvPTWjgEHRi3OFC', 'user@todoco.com', '[]');

INSERT INTO `task` (`id`, `created_at`, `title`, `content`, `is_done`, `user_id`) VALUES
(2, '2019-10-22 22:02:57', 'Taches Admin', 'Taches Admin', 0, 1),
(3, '2019-10-22 22:03:20', 'Taches User 1', 'Taches User 1', 0, 2),
(4, '2019-10-22 22:03:40', 'Tache User 2', 'Tache User 2', 0, 2),
(5, '2019-10-22 22:04:07', 'Tache Anonyme', 'Tache Anonyme', 0, NULL);