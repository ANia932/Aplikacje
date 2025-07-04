--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5 (Debian 17.5-1.pgdg120+1)
-- Dumped by pg_dump version 17.5 (Debian 17.5-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: archiwum_zamowien; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.archiwum_zamowien (
    id integer NOT NULL,
    numer_zamowienia character varying(50),
    nazwa character varying(100),
    ilosc integer,
    numer_produkcji character varying(50),
    miejsce_dostawy character varying(100),
    magazyn character varying(100),
    status character varying(50),
    osoba_odpowiedzialna character varying(100),
    data_dostawy date,
    data_dodania timestamp without time zone,
    postep integer,
    czy_zrealizowano boolean DEFAULT false
);


ALTER TABLE public.archiwum_zamowien OWNER TO postgres;

--
-- Name: archiwum_zamowien_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.archiwum_zamowien_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.archiwum_zamowien_id_seq OWNER TO postgres;

--
-- Name: archiwum_zamowien_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.archiwum_zamowien_id_seq OWNED BY public.archiwum_zamowien.id;


--
-- Name: komponenty; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.komponenty (
    id text NOT NULL,
    nazwa text NOT NULL,
    kategoria text,
    technologia text,
    waga numeric(5,2),
    dlugosc integer,
    szerokosc integer,
    wysokosc integer,
    cena numeric(8,2),
    waluta text,
    czas_produkcji integer,
    data_oferty date DEFAULT CURRENT_DATE,
    odwolana boolean DEFAULT false
);


ALTER TABLE public.komponenty OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    imie text NOT NULL,
    nazwisko text NOT NULL,
    email text NOT NULL,
    password text NOT NULL,
    stanowisko text,
    dzial text,
    is_active boolean DEFAULT true,
    data_rejestracji timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: zamowienia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zamowienia (
    id integer NOT NULL,
    numer_zamowienia character varying(20) NOT NULL,
    komponent text NOT NULL,
    ilosc integer NOT NULL,
    numer_produkcji character varying(50),
    lokalizacja text,
    miejsce_dostawy text,
    status text DEFAULT 'Nowe'::text,
    przypisanie text,
    termin_dostarczenia date,
    realna_dostawa date,
    postep integer DEFAULT 0,
    data_dodania timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    archiwum boolean DEFAULT false
);


ALTER TABLE public.zamowienia OWNER TO postgres;

--
-- Name: zamowienia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zamowienia_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zamowienia_id_seq OWNER TO postgres;

--
-- Name: zamowienia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.zamowienia_id_seq OWNED BY public.zamowienia.id;


--
-- Name: archiwum_zamowien id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.archiwum_zamowien ALTER COLUMN id SET DEFAULT nextval('public.archiwum_zamowien_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: zamowienia id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zamowienia ALTER COLUMN id SET DEFAULT nextval('public.zamowienia_id_seq'::regclass);


--
-- Data for Name: archiwum_zamowien; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.archiwum_zamowien (id, numer_zamowienia, nazwa, ilosc, numer_produkcji, miejsce_dostawy, magazyn, status, osoba_odpowiedzialna, data_dostawy, data_dodania, postep, czy_zrealizowano) FROM stdin;
\.


--
-- Data for Name: komponenty; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.komponenty (id, nazwa, kategoria, technologia, waga, dlugosc, szerokosc, wysokosc, cena, waluta, czas_produkcji, data_oferty, odwolana) FROM stdin;
KOF473C06	Rama Z1	Zabudowy	Metale	15.00	2500	1200	60	540.00	PLN	21	2025-06-13	f
KOFDF7797	Rama Z1	Zabudowy	Metale	15.00	2500	1200	60	540.00	PLN	21	2025-06-13	f
KOFDBAF23	Rama Z1 Wolnostoj─ůca 	Zabudowy	Metale	20.00	2500	1200	120	350.00	PLN	21	2025-06-14	f
KOF7EE19C	Rama Z1	Zabudowy	Metale	15.00	2500	1200	60	580.00	PLN	21	2025-06-14	f
KOF1D5D54	Rama Z1 Wolnostoj─ůca 	Zabudowy	Metale	20.00	2500	1200	120	450.00	PLN	21	2025-06-14	f
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, imie, nazwisko, email, password, stanowisko, dzial, is_active, data_rejestracji) FROM stdin;
4	ANNA	LATOCHA	anialatocha2002@gmail.com	$2y$10$y0nkRREup.DAwbkeJIm20.nsSXLSBmy0n6rQEeFiSDBUryJz/LiGO	zaopatrzeniowiec	biuro	t	2025-06-13 13:10:32.890994
5	EMILIA	KOWLASKA	emiliagora@gmail.com	$2y$10$UknYRhGJfHbHRjfu889irOxOHcIWwpsrWxtmhcl2k1QKDOgb0vfAu	pakowacz	pakowalnia	t	2025-06-13 13:11:51.014003
\.


--
-- Data for Name: zamowienia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.zamowienia (id, numer_zamowienia, komponent, ilosc, numer_produkcji, lokalizacja, miejsce_dostawy, status, przypisanie, termin_dostarczenia, realna_dostawa, postep, data_dodania, archiwum) FROM stdin;
11	ZAM20250616-006	Rama Z1	50	PR029	SI GDA┼âSK	Magazyn ERGO	Potwierdzone	JAKUB KOWALSKI	2025-06-30	\N	0	2025-06-16 13:24:58.956099	f
10	ZAM20250616-005	Rama Z1	32	PR030	SI KRAK├ôW	Magazyn ERGO	Potwierdzone	JAKUB KOWALSKI	2025-06-23	\N	5	2025-06-16 13:24:28.37982	f
17	ZAM20250616-009	Rama Z1	50	PR028	SI GDA┼âSK	Magazyn ERGO	Potwierdzone	JAKUB KOWALSKI	2025-07-01	\N	0	2025-06-16 14:09:30.501981	f
18	ZAM20250616-010	Rama Z1 Wolnostoj─ůca 	50	PR024	SI GDA┼âSK	Magazyn ERGO	Potwierdzone	JAKUB KOWALSKI	2025-06-23	\N	0	2025-06-16 14:09:44.195457	f
1	ZAM20250614-001	Rama Z1 Wolnostoj─ůca 	21	PR023	SI JAROS┼üAW	Magazyn ERGO	Dostarczone	JAKUB KOWALSKI	2025-06-27	\N	100	2025-06-14 22:39:37.575624	t
2	ZAM20250616-001	Rama Z1	50	PR024	SI JAROS┼üAW	Magazyn ERGO	Dostarczone		2025-06-16	\N	100	2025-06-16 07:06:46.750854	t
3	ZAM20250616-002	Rama Z1 Wolnostoj─ůca 	30	PR025	SI JAROS┼üAW	Magazyn ERGO	Dostarczone	JAKUB KOWALSKI	2025-06-16	\N	100	2025-06-16 07:11:55.564483	t
9	ZAM20250616-008	Rama Z1	21	PR028	SI KRAK├ôW	Magazyn ERGO	Dostarczone	JAKUB KOWALSKI	2025-06-23	\N	100	2025-06-16 07:15:48.627322	t
8	ZAM20250616-007	Rama Z1 Wolnostoj─ůca 	50	PR028	SI KRAK├ôW	Magazyn ERGO	Dostarczone	JAKUB KOWALSKI	2025-06-23	\N	100	2025-06-16 07:15:17.912841	t
\.


--
-- Name: archiwum_zamowien_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.archiwum_zamowien_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 5, true);


--
-- Name: zamowienia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zamowienia_id_seq', 18, true);


--
-- Name: archiwum_zamowien archiwum_zamowien_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.archiwum_zamowien
    ADD CONSTRAINT archiwum_zamowien_pkey PRIMARY KEY (id);


--
-- Name: komponenty komponenty_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.komponenty
    ADD CONSTRAINT komponenty_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: zamowienia zamowienia_numer_zamowienia_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zamowienia
    ADD CONSTRAINT zamowienia_numer_zamowienia_key UNIQUE (numer_zamowienia);


--
-- Name: zamowienia zamowienia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zamowienia
    ADD CONSTRAINT zamowienia_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

