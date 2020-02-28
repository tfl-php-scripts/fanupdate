-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 28, 2020 at 08:55 PM
-- Server version: 5.5.62
-- PHP Version: 7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `fanupdate`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
    `entry_id` int(6) UNSIGNED NOT NULL,
    `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `title` varchar(50) NOT NULL DEFAULT '',
    `body` text NOT NULL,
    `is_public` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    `comments_on` tinyint(1) UNSIGNED NOT NULL DEFAULT '1'
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`entry_id`, `added`, `title`, `body`, `is_public`, `comments_on`) VALUES
    (1, '2010-02-21 20:48:40', 'EntryOne', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (2, '2020-02-28 20:51:07', 'EntryTwo', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (3, '2010-02-21 20:48:40', 'EntryOne123', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (4, '2010-02-28 20:48:40', 'EntryTwo456', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (5, '2010-02-21 20:48:40', 'EntryOnetwo', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (6, '2010-02-28 20:48:40', 'EntryTwofree', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (7, '2010-02-21 20:48:40', 'EntryOne123', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1),
    (8, '2010-02-28 20:48:40', 'EntryTwo456111', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt ante sed pulvinar interdum. Sed et lacus feugiat, commodo nibh quis, suscipit lectus. Aenean sollicitudin rutrum tortor, at scelerisque tortor dapibus eu. Sed ultricies, ante vitae molestie auctor, sem arcu eleifend nibh, euismod ultrices ipsum diam eu metus. Mauris eu lacinia lectus. Fusce ornare sem posuere urna malesuada ullamcorper ac non velit. Aliquam quis quam gravida, pellentesque tortor quis, porttitor risus. Curabitur porttitor vulputate tortor, nec euismod eros semper vulputate. Morbi vestibulum velit vel mi placerat, nec imperdiet massa convallis.\r\n\r\nProin porta nec diam a commodo. Aliquam et nulla hendrerit, vulputate est non, tincidunt mi. Nam vel consequat purus. Vivamus at aliquam neque. Ut commodo scelerisque porttitor. Suspendisse potenti. Aliquam condimentum nibh ligula, id sagittis turpis consectetur ac. Fusce in gravida augue.\r\n\r\nNullam placerat dictum orci et iaculis. Maecenas magna mi, aliquet sed nisi at, tincidunt scelerisque lectus. Etiam quis venenatis metus. Suspendisse et diam molestie, pulvinar nisl vel, maximus mi. Sed lectus ex, imperdiet non elementum a, venenatis nec mauris. Maecenas pretium, erat sed tincidunt rhoncus, magna libero commodo enim, a fringilla erat nunc in diam. Vestibulum ante justo, tincidunt eget laoreet sed, cursus ut velit.\r\n\r\nSuspendisse pulvinar augue et mollis mattis. Morbi sed dolor sed enim lobortis maximus sed et augue. Curabitur dolor ante, feugiat tempor fringilla nec, egestas non lacus. Phasellus mattis, nibh in venenatis consequat, dui ligula laoreet eros, vel finibus est dui eget mauris. Nullam egestas risus sed sagittis lacinia. Sed pharetra velit non nulla pellentesque condimentum. Vestibulum faucibus mi id mauris hendrerit, in egestas arcu rhoncus. Donec massa justo, rutrum a massa quis, vehicula semper ante. Phasellus id velit sit amet ex dapibus semper ut et turpis. Donec tincidunt, risus quis pretium rutrum, tortor tortor malesuada sem, eu mollis diam arcu ac lorem. Ut luctus est nec massa ultricies consequat. Curabitur suscipit risus quis mollis mattis. Fusce commodo, nisl vitae porttitor mattis, massa sapien sodales erat, sit amet posuere nibh ligula vitae velit. Aliquam nec turpis mauris.\r\n\r\nVivamus egestas elit sed erat condimentum, a efficitur nisl blandit. Donec maximus vehicula auctor. Nam in arcu id ipsum semper posuere ac in ipsum. Curabitur vitae odio pharetra, suscipit quam nec, mollis turpis. Phasellus lacinia ornare libero at consequat. Praesent eu metus tellus. Aenean efficitur in arcu ut facilisis. Curabitur mollis diam at rhoncus eleifend. Fusce faucibus volutpat velit, ut tincidunt leo consectetur ac. Sed interdum sodales dui ut lobortis. Sed tempor lobortis urna. Suspendisse id augue dapibus, commodo nunc nec, gravida nisl.', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `blog_blacklist`
--

CREATE TABLE `blog_blacklist` (
    `badword` varchar(50) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_blacklist`
--

INSERT INTO `blog_blacklist` (`badword`) VALUES
    ('blackjack'),
    ('casino'),
    ('cialis'),
    ('diazepam'),
    ('gambling'),
    ('hoodia'),
    ('hydrocodone'),
    ('kasino'),
    ('levitra'),
    ('phentermine'),
    ('ringtones'),
    ('viagra'),
    ('webcam');

-- --------------------------------------------------------

--
-- Table structure for table `blog_category`
--

CREATE TABLE `blog_category` (
    `fl_id` int(6) UNSIGNED NOT NULL,
    `fl_subject` varchar(50) NOT NULL DEFAULT ''
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_category`
--

INSERT INTO `blog_category` (`fl_id`, `fl_subject`) VALUES
    (1, 'CategoryOne'),
    (2, 'CategoryTwo');

-- --------------------------------------------------------

--
-- Table structure for table `blog_catjoin`
--

CREATE TABLE `blog_catjoin` (
    `entry_id` int(6) UNSIGNED NOT NULL DEFAULT '0',
    `cat_id` int(6) UNSIGNED NOT NULL DEFAULT '0'
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_catjoin`
--

INSERT INTO `blog_catjoin` (`entry_id`, `cat_id`) VALUES
    (2, 1),
    (3, 1),
    (4, 2),
    (5, 1),
    (6, 1),
    (6, 2),
    (7, 1),
    (7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `blog_catoptions`
--

CREATE TABLE `blog_catoptions` (
    `cat_id` int(6) UNSIGNED NOT NULL DEFAULT '0',
    `comments_on` tinyint(1) UNSIGNED DEFAULT NULL,
    `date_format` varchar(30) DEFAULT NULL,
    `gravatar_on` tinyint(1) UNSIGNED DEFAULT NULL,
    `gravatar_default` varchar(100) DEFAULT NULL,
    `gravatar_size` varchar(2) DEFAULT NULL,
    `gravatar_rating` varchar(2) DEFAULT NULL,
    `entry_template` text,
    `comment_template` text
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_catoptions`
--

INSERT INTO `blog_catoptions` (`cat_id`, `comments_on`, `date_format`, `gravatar_on`, `gravatar_default`, `gravatar_size`, `gravatar_rating`, `entry_template`, `comment_template`) VALUES
    (1, 1, '', 1, '', '', '', '', ''),
    (2, 1, '', 1, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
    `comment_id` int(6) UNSIGNED NOT NULL,
    `entry_id` int(6) UNSIGNED NOT NULL DEFAULT '0',
    `name` varchar(30) NOT NULL DEFAULT '',
    `email` varchar(100) NOT NULL DEFAULT '',
    `url` varchar(100) NOT NULL DEFAULT '',
    `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `comment` text NOT NULL,
    `approved` tinyint(1) NOT NULL DEFAULT '0',
    `points` int(4) NOT NULL DEFAULT '0'
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`comment_id`, `entry_id`, `name`, `email`, `url`, `added`, `comment`, `approved`, `points`) VALUES
    (1, 2, 'Commentator', 'myemail@jghdfjkjkjgkdjgkdf.com', 'http://exampleeeeee.com', '2020-02-28 20:54:08', 'Comment', 1, -3);

-- --------------------------------------------------------

--
-- Table structure for table `blog_options`
--

CREATE TABLE `blog_options` (
    `optkey` varchar(30) NOT NULL,
    `optvalue` text NOT NULL,
    `optdesc` text NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_options`
--

INSERT INTO `blog_options` (`optkey`, `optvalue`, `optdesc`) VALUES
    ('admin_email', 'me@example.com', 'Your email address, so you can be notified of new comments.'),
    ('site_name', 'My Site', 'The name of your site or blog.'),
    ('blog_page', 'http://localhost:8033/index.php', 'The URL of your blog page (needed for RSS feeds).'),
    ('install_path', '/app/public/fanupdate', 'The full server path to your FanUpdate directory. NO trailing slash. Ex: /home/username/public_html/fanupdate'),
    ('install_url', 'http://localhost:8033/fanupdate', 'The URL to your FanUpdate directory. NO trailing slash. Ex: http://example.com/fanupdate'),
    ('comments_on', 'y', 'Allow comments? y or n'),
    ('date_format', 'M j, Y', 'PHP date format string. See options here: http://php.net/date'),
    ('email_new_comments', 'y', 'Email you about new comments? y or n'),
    ('comment_moderation', 'n', 'Hold new comments for moderation? y or n'),
    ('captcha_on', 'n', 'Protect comments with captcha? y or n'),
    ('gravatar_on', 'n', 'Use Gravatars? y or n'),
    ('gravatar_default', 'http://localhost:8033/fanupdate/gravatar.png', 'URL of default Gravatar image. Ex: http://example.com/fanupdate/gravatar.png'),
    ('gravatar_size', '80', 'Gravatar image dimension in pixels (80 max).'),
    ('gravatar_rating', 'G', 'Highest allowable Gravatar image rating: G, PG, R, X'),
    ('entry_template', '<h2><a href=\"{{url}}\" title=\"permanent link to this post\">{{title}}</a></h2>\r\n\r\n<p class=\"catfile\">Posted {{date}}. Filed under {{category}}. {{comment_link}}</p>\r\n\r\n{{body}}', 'See readme.txt for template variables.'),
    ('comment_template', '{{gravatar}}\r\n\r\n<p class=\"commenter\">On \r\n<a href=\"#comment{{id}}\" title=\"permanent link to this comment\">{{date}}</a>\r\n{{name}} said:</p>\r\n\r\n{{body}}', 'See readme.txt for template variables.'),
    ('num_per_page', '20', 'Number of items displayed per page for pagination.'),
    ('abstract_word_count', '0', 'The number of words to display for post summaries. 0 to turn off.'),
    ('comment_form_template', '<h3 id=\"postcomment\">Post A Comment</h3>\r\n\r\n<!-- MODERATION -->\r\n<p id=\"cmt-moderation\"><strong>Comment moderation is currently turned on.</strong> Your comment will not be displayed until it has been approved by the site owner.</p>\r\n<!-- END MODERATION -->\r\n\r\n<p><label for=\"name\">Name:</label>\r\n<input type=\"text\" id=\"name\" name=\"name\" maxlength=\"20\" size=\"25\" value=\"{{fanuname}}\" />\r\n<label for=\"remember_me\" class=\"checkbox\"><input type=\"checkbox\" id=\"remember_me\" name=\"remember_me\" value=\"1\" checked=\"checked\" /> Remember?</label></p>\r\n\r\n<p><label for=\"email\">Email:</label>\r\n<input type=\"text\" id=\"email\" name=\"email\" maxlength=\"70\" size=\"25\" value=\"{{fanuemail}}\" /></p>\r\n\r\n<p><label for=\"url\">URL:</label>\r\n<input type=\"text\" id=\"url\" name=\"url\" maxlength=\"70\" size=\"25\" value=\"{{fanuurl}}\" /></p>\r\n\r\n<p><label for=\"myta\">Comment:</label>\r\n<textarea id=\"myta\" name=\"comment\" cols=\"50\" rows=\"8\"></textarea></p>\r\n\r\n<!-- CAPTCHA -->\r\n<p><label for=\"captcha\">Captcha:</label>\r\n<img id=\"captcha-img\" src=\"{{captcha_image}}\" alt=\"\" />\r\n<input type=\"text\" id=\"captcha\" name=\"captcha\" /></p>\r\n<!-- END CAPTCHA -->\r\n\r\n<p><input type=\"submit\" id=\"submit\" name=\"submit_comment\" value=\"Post Comment\" class=\"submit\" /></p>\r\n\r\n<p id=\"cmt-rules\">Your email is only for accessing <a href=\"http://www.gravatar.com/\">gravatar.com</a>. No <abbr title=\"HyperText Markup Language\">HTML</abbr> allowed; some formatting can be applied via the buttons above the textarea.</p>', 'Customize your comment form. Don\'t change the names of the inputs or the CAPTCHA and MODERATION comments, but everything else may be modified. See readme.txt for template variables.'),
('footer_template', '<div class=\"archivelink\">\r\n<form action=\"{{main_url}}\" method=\"get\">\r\n<p>\r\n<a href=\"{{main_url}}\">main</a> &middot;\r\n<a href=\"{{archive_url}}\">archive</a> &middot;\r\n<a class=\"rss\" href=\"{{rss_url}}\">feed</a> &middot;\r\n<input type=\"text\" name=\"q\" value=\"\" />\r\n<input type=\"submit\" value=\"Search\" class=\"button\" />\r\n</p>\r\n</form>\r\n</div><!-- END .archivelink -->\r\n\r\n<div class=\"credit\">\r\n<p>Powered by <a href=\"{{fanupdate_url}}\" target=\"_blank\" class=\"ext\">FanUpdate {{fanupdate_version}}</a> / Original script by <a href=\"{{fanupdate_original_url}}\" target=\"_blank\" class=\"ext\">{{fanupdate_original_url}}</a></p>\r\n</div><!-- END .credit -->', 'Put your footer blog navigation here. See readme.txt for template variables.'),
    ('_last_update_check', '2010-02-28', 'Last time prism-perfect.net was checked for latest release.'),
    ('_last_update_version', '2.3', 'Most recent stable version.'),
    ('points_scoring', 'y', 'Use point scoring system to block spam. y or n'),
    ('points_approval_threshold', '1', 'Minimum points for automatic comment approval. Default: 1'),
    ('points_pending_threshold', '-4', 'Minimum points for comment to be moderated. Any less points and it is spam. Default: -4'),
    ('_db_version', '2.2.1', 'Version of current database schema.'),
    ('timezone_offset', '1', 'Hours difference between your location and GMT. Default: 0'),
    ('_server_tz_offset', '0', 'Hours difference between server time and GMT.'),
    ('ajax_comments', 'y', 'Use ajax commenting? Turn off if you have problems. y or n');

-- --------------------------------------------------------

--
-- Table structure for table `blog_smilies`
--

    CREATE TABLE `blog_smilies` (
    `smiley` varchar(10) NOT NULL,
    `image` varchar(50) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_smilies`
--

    INSERT INTO `blog_smilies` (`smiley`, `image`) VALUES
    (':)', 'emoticon_smile.png'),
    (':D', 'emoticon_grin.png'),
    ('XD', 'emoticon_evilgrin.png'),
    (':O', 'emoticon_surprised.png'),
    (':P', 'emoticon_tongue.png'),
    (':(', 'emoticon_unhappy.png'),
    (';D', 'emoticon_wink.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
    ALTER TABLE `blog`
    ADD PRIMARY KEY (`entry_id`);
    ALTER TABLE `blog` ADD FULLTEXT KEY `title` (`title`,`body`);

--
-- Indexes for table `blog_blacklist`
--
    ALTER TABLE `blog_blacklist`
    ADD PRIMARY KEY (`badword`);

--
-- Indexes for table `blog_category`
--
    ALTER TABLE `blog_category`
    ADD PRIMARY KEY (`fl_id`);

--
-- Indexes for table `blog_catjoin`
--
    ALTER TABLE `blog_catjoin`
    ADD PRIMARY KEY (`entry_id`,`cat_id`);

--
-- Indexes for table `blog_catoptions`
--
    ALTER TABLE `blog_catoptions`
    ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `blog_comments`
--
    ALTER TABLE `blog_comments`
    ADD PRIMARY KEY (`comment_id`),
    ADD KEY `entry` (`entry_id`),
    ADD KEY `approved` (`approved`);
    ALTER TABLE `blog_comments` ADD FULLTEXT KEY `name` (`name`,`comment`);

--
-- Indexes for table `blog_options`
--
    ALTER TABLE `blog_options`
    ADD PRIMARY KEY (`optkey`);

--
-- Indexes for table `blog_smilies`
--
    ALTER TABLE `blog_smilies`
    ADD PRIMARY KEY (`smiley`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
    ALTER TABLE `blog`
    MODIFY `entry_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blog_category`
--
    ALTER TABLE `blog_category`
    MODIFY `fl_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_comments`
--
    ALTER TABLE `blog_comments`
    MODIFY `comment_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
    COMMIT;
